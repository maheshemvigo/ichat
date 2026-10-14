<?php

namespace App\Services;

use App\ChatUser;
use App\Message;
use Auth;
use DB;

class SimulationHistoryCollector
{
    /**
     * All active chat thread IDs for a simulation.
     *
     * @param int $simulationId
     * @return array
     */
    public function chatUserIdsForSimulation($simulationId)
    {
        return ChatUser::where('simulation_id', $simulationId)
            ->where('status', 0)
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->toArray();
    }

    /**
     * Collect message collections for selected chat_user IDs (same logic as legacy download).
     *
     * @param array $chatUserIds
     * @return array
     */
    /**
     * Collect all chat threads for a simulation (works in queue — no auth required).
     *
     * @param int $simulationId
     * @return array
     */
    public function collectForSimulation($simulationId)
    {
        $simulation = DB::table('simulations')->where('id', $simulationId)->first();

        if (!$simulation) {
            return [];
        }

        return $this->collectForChatUsers(
            $this->chatUserIdsForSimulation($simulationId),
            $simulation->created_by
        );
    }

    /**
     * @param array $chatUserIds
     * @param int|null $adminUserId Simulation owner (required when running outside HTTP / queue jobs)
     * @return array
     */
    public function collectForChatUsers(array $chatUserIds, $adminUserId = null)
    {
        $reportData = [];

        if ($adminUserId === null && auth()->check()) {
            $adminUserId = auth()->id();
        }

        foreach ($chatUserIds as $user) {
            $recieveId = ChatUser::where([['status', 0], ['id', $user]])->value('user_id');
            $chatUserIdentificationId = ChatUser::where([['status', 0], ['id', $user]])->value('admin_user_id');
            $simulationRow = DB::table('chat_users')
                ->join('simulations', 'simulations.id', '=', 'chat_users.simulation_id')
                ->select('simulations.created_by', 'simulations.simulation_name', 'simulations.id as simulation_id')
                ->where([['chat_users.status', 0], ['chat_users.id', $user]])
                ->first();

            if (!$simulationRow) {
                continue;
            }

            $ownerId = $adminUserId ?: $simulationRow->created_by;

            $messages = Message::with('chat_users_data.simulation_data', 'user_data', 'upload_data')
                ->where(function ($query) use ($simulationRow, $recieveId, $chatUserIdentificationId, $ownerId) {
                    $query->where([
                        'user_id' => $simulationRow->created_by,
                        'receiver_id' => $recieveId,
                        'chat_user_identification_id' => $chatUserIdentificationId,
                    ])->orWhere([
                        'user_id' => $recieveId,
                        'receiver_id' => $ownerId,
                        'chat_user_identification_id' => $chatUserIdentificationId,
                    ]);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            if ($messages->isEmpty()) {
                continue;
            }

            $reportData[] = [
                'chat_user_id' => (int) $user,
                'simulation_id' => $simulationRow->simulation_id,
                'simulation_name' => $simulationRow->simulation_name,
                'messages' => $messages,
            ];
        }

        return $reportData;
    }

    /**
     * Build plain-text transcripts per selected thread for LLM input.
     *
     * @param array $reportData
     * @return array
     */
    public function buildTranscripts(array $reportData)
    {
        $threads = [];

        foreach ($reportData as $block) {
            $lines = [];
            $threadName = '';
            $simulationName = $block['simulation_name'] ?? 'Simulation';

            foreach ($block['messages'] as $message) {
                if ($message->chat_users_data) {
                    $threadName = $message->chat_users_data->name;
                    if ($message->chat_users_data->simulation_data) {
                        $simulationName = $message->chat_users_data->simulation_data->simulation_name;
                    }
                }

                $speaker = $message->user_data ? $message->user_data->name : 'Unknown';
                $timestamp = $message->created_at
                    ? (is_object($message->created_at) && method_exists($message->created_at, 'format')
                        ? $message->created_at->format('Y-m-d H:i:s')
                        : (string) $message->created_at)
                    : '';
                $content = $this->formatMessageContent($message);

                if ($content !== '') {
                    $lines[] = "[{$timestamp}] {$speaker}: {$content}";
                }
            }

            if (count($lines) === 0) {
                continue;
            }

            $threads[] = [
                'chat_user_id' => $block['chat_user_id'],
                'simulation_name' => $simulationName,
                'thread_name' => $threadName ?: 'Chat thread',
                'message_count' => count($lines),
                'transcript' => implode("\n", $lines),
            ];
        }

        return $threads;
    }

    protected function formatMessageContent($message)
    {
        if (!empty($message->message)) {
            return strip_tags($message->message);
        }

        if (!empty($message->image)) {
            $label = $message->upload_data ? $message->upload_data->name : 'Image';
            return '[Shared image: ' . $label . ']';
        }

        if (!empty($message->video)) {
            $label = $message->upload_data ? $message->upload_data->name : 'Video';
            return '[Shared video: ' . $label . ']';
        }

        if (!empty($message->doc)) {
            $label = $message->upload_data ? $message->upload_data->name : 'Document';
            return '[Shared document: ' . $label . ']';
        }

        if (!empty($message->link)) {
            return '[Link: ' . $message->link . ']';
        }

        return '';
    }
}
