<?php

namespace App\Services;

use Carbon\Carbon;

class LlmStructuredReportService
{
    protected $collector;

    public function __construct(SimulationHistoryCollector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @param array $chatUserIds
     * @return array
     */
    public function generateFromChatUserIds(array $chatUserIds)
    {
        $reportData = $this->collector->collectForChatUsers($chatUserIds);
        $threads = $this->collector->buildTranscripts($reportData);

        if (empty($threads)) {
            return $this->emptyReport('No messages found for the selected chat threads.');
        }

        $threads = array_values(array_filter($threads, function ($thread) {
            return $thread['message_count'] > 0 && trim($thread['transcript']) !== '';
        }));

        if (empty($threads)) {
            return $this->emptyReport('No messages found for the selected chat threads.');
        }

        $threadReports = [];
        foreach ($threads as $thread) {
            $threadReports[] = $this->structureThread($thread);
        }

        return $this->mergeThreadReports($threadReports);
    }

    /**
     * Full simulation summary (all chat threads) for queue + S3 storage.
     *
     * @param int $simulationId
     * @return array
     */
    public function generateForSimulation($simulationId)
    {
        $blocks = $this->collector->collectForSimulation($simulationId);

        if (empty($blocks)) {
            return $this->emptyReport('No messages found for this simulation.');
        }

        $threads = $this->collector->buildTranscripts($blocks);

        if (empty($threads)) {
            return $this->emptyReport('No messages found for this simulation.');
        }

        if (count($threads) === 1) {
            $report = $this->structureThread($this->limitTranscriptForLlm($threads[0]));

            return [
                'combined' => false,
                'report' => $report,
                'sections' => [$report],
            ];
        }

        $mergedThread = $this->mergeTranscriptsForSimulation($threads);
        $report = $this->structureThread($this->limitTranscriptForLlm($mergedThread));

        return [
            'combined' => false,
            'report' => $report,
            'sections' => [$report],
        ];
    }

    /**
     * Combine all participant threads into one transcript for a single simulation summary.
     *
     * @param array $threads
     * @return array
     */
    protected function mergeTranscriptsForSimulation(array $threads)
    {
        $parts = [];
        $totalMessages = 0;

        foreach ($threads as $thread) {
            $totalMessages += (int) $thread['message_count'];
            $parts[] = '--- ' . ($thread['thread_name'] ?: 'Chat thread') . " ---\n" . $thread['transcript'];
        }

        return [
            'chat_user_id' => 0,
            'simulation_name' => $threads[0]['simulation_name'],
            'thread_name' => 'All participants',
            'message_count' => $totalMessages,
            'transcript' => implode("\n\n", $parts),
        ];
    }

    /**
     * Reduce very large transcripts before LLM calls (avoids 50+ chunk API requests).
     *
     * @param array $thread
     * @return array
     */
    protected function limitTranscriptForLlm(array $thread)
    {
        $maxLines = (int) config('llm_report.simulation_max_llm_lines', 500);
        $lines = array_values(array_filter(explode("\n", $thread['transcript']), 'strlen'));
        $total = count($lines);

        if ($total <= $maxLines) {
            return $thread;
        }

        $headSize = (int) floor($maxLines * 0.3);
        $tailSize = (int) floor($maxLines * 0.3);
        $sampleSize = max(0, $maxLines - $headSize - $tailSize - 1);

        $head = array_slice($lines, 0, $headSize);
        $tail = array_slice($lines, -$tailSize);
        $middle = array_slice($lines, $headSize, max(0, $total - $headSize - $tailSize));

        $sampled = [];
        if ($sampleSize > 0 && count($middle) > 0) {
            $step = max(1, (int) floor(count($middle) / $sampleSize));
            for ($i = 0; $i < count($middle) && count($sampled) < $sampleSize; $i += $step) {
                $sampled[] = $middle[$i];
            }
        }

        $omitted = $total - count($head) - count($tail) - count($sampled);
        $marker = '[... ' . $omitted . ' messages omitted from LLM input; full simulation has '
            . (int) $thread['message_count'] . ' messages ...]';

        $thread['transcript'] = implode("\n", array_merge($head, [$marker], $sampled, $tail));
        $thread['llm_line_count'] = count($head) + 1 + count($sampled) + count($tail);
        $thread['transcript_trimmed'] = true;

        return $thread;
    }

    protected function structureThread(array $thread)
    {
        $transcript = $thread['transcript'];
        $useLlm = config('llm_report.enabled') && !empty(config('llm_report.api_key'));

        if (!$useLlm || trim($transcript) === '') {
            return $this->fallbackThreadReport($thread);
        }

        $lineCount = isset($thread['llm_line_count'])
            ? (int) $thread['llm_line_count']
            : (int) $thread['message_count'];

        $chunks = $this->chunkTranscript($transcript, $lineCount);

        try {
            if (count($chunks) === 1) {
                $userPrompt = "Simulation: {$thread['simulation_name']}\nThread: {$thread['thread_name']}\n\nTranscript:\n{$chunks[0]}";
                $parsed = $this->callLlmJson(config('llm_report.system_prompt'), $userPrompt);
            } else {
                $chunkSummaries = [];
                foreach ($chunks as $index => $chunk) {
                    $chunkPrompt = config('llm_report.chunk_prompt') . "\n\nChunk " . ($index + 1) . " of " . count($chunks) . ":\n" . $chunk;
                    $chunkSummaries[] = $this->callLlmJson(config('llm_report.system_prompt'), $chunkPrompt);
                }
                $mergeInput = "Simulation: {$thread['simulation_name']}\nThread: {$thread['thread_name']}\n\nChunk analyses:\n"
                    . json_encode($chunkSummaries, JSON_PRETTY_PRINT);
                $parsed = $this->callLlmJson(
                    config('llm_report.system_prompt') . "\n\n" . config('llm_report.merge_prompt'),
                    $mergeInput
                );
                $parsed['appendix_notes'] = trim(($parsed['appendix_notes'] ?? '') . ' Report generated using chunked processing (' . count($chunks) . ' chunks).');
            }

            $report = $this->normalizeReport($parsed, $thread);

            if (!empty($thread['transcript_trimmed'])) {
                $note = 'LLM analysis used a sampled subset of a large chat (' . (int) $thread['message_count'] . ' total messages).';
                $report['appendix_notes'] = trim(($report['appendix_notes'] ?? '') . ' ' . $note);
            }

            return $report;
        } catch (\Exception $e) {
            $fallback = $this->fallbackThreadReport($thread);
            $fallback['appendix_notes'] = 'LLM unavailable: ' . $e->getMessage() . '. Showing rule-based summary.';
            return $fallback;
        }
    }

    protected function chunkTranscript($transcript, $messageCount)
    {
        $charLimit = config('llm_report.chunk_char_limit');
        $messageLimit = config('llm_report.chunk_message_limit');

        if (strlen($transcript) <= $charLimit && $messageCount <= $messageLimit) {
            return [$transcript];
        }

        $lines = explode("\n", $transcript);
        $chunks = [];
        $current = [];
        $currentChars = 0;

        foreach ($lines as $line) {
            $lineLen = strlen($line) + 1;
            if (count($current) >= $messageLimit || ($currentChars + $lineLen > $charLimit && count($current) > 0)) {
                $chunks[] = implode("\n", $current);
                $current = [];
                $currentChars = 0;
            }
            $current[] = $line;
            $currentChars += $lineLen;
        }

        if (count($current) > 0) {
            $chunks[] = implode("\n", $current);
        }

        return $chunks ?: [$transcript];
    }

    protected function callLlmJson($systemPrompt, $userPrompt)
    {
        $apiKey = config('llm_report.api_key');
        $payload = [
            'model' => config('llm_report.model'),
            'temperature' => 0.3,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ];

        $client = new \GuzzleHttp\Client(['timeout' => config('llm_report.timeout')]);
        $response = $client->post(config('llm_report.api_url'), [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $body = json_decode((string) $response->getBody(), true);
        $content = $body['choices'][0]['message']['content'] ?? '';

        return $this->parseJsonFromLlm($content);
    }

    protected function parseJsonFromLlm($content)
    {
        $content = trim($content);
        $content = preg_replace('/^```json\s*/i', '', $content);
        $content = preg_replace('/^```\s*/i', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('LLM returned invalid JSON.');
        }

        return $decoded;
    }

    protected function normalizeReport(array $parsed, array $thread)
    {
        return [
            'report_title' => $parsed['report_title'] ?? 'Simulation Debrief Report',
            'simulation_name' => $parsed['simulation_name'] ?? $thread['simulation_name'],
            'thread_name' => $parsed['thread_name'] ?? $thread['thread_name'],
            'generated_at' => $parsed['generated_at'] ?? Carbon::now()->toIso8601String(),
            'executive_summary' => $parsed['executive_summary'] ?? '',
            'key_insights' => $this->ensureList($parsed['key_insights'] ?? []),
            'action_items' => $this->normalizeActionItems($parsed['action_items'] ?? []),
            'participants' => $this->ensureList($parsed['participants'] ?? []),
            'topics_discussed' => $this->ensureList($parsed['topics_discussed'] ?? []),
            'decisions_made' => $this->ensureList($parsed['decisions_made'] ?? []),
            'risks_and_concerns' => $this->ensureList($parsed['risks_and_concerns'] ?? []),
            'recommendations' => $this->ensureList($parsed['recommendations'] ?? []),
            'notable_quotes' => $parsed['notable_quotes'] ?? [],
            'appendix_notes' => $parsed['appendix_notes'] ?? '',
            'message_count' => $thread['message_count'],
        ];
    }

    protected function fallbackThreadReport(array $thread)
    {
        $lines = array_filter(explode("\n", $thread['transcript']));
        $participants = [];
        foreach ($lines as $line) {
            if (preg_match('/\] ([^:]+):/', $line, $m)) {
                $participants[$m[1]] = true;
            }
        }

        $preview = implode("\n", array_slice($lines, 0, 5));
        $summary = "This report was generated without LLM enrichment. The selected thread \"{$thread['thread_name']}\" "
            . "under simulation \"{$thread['simulation_name']}\" contains {$thread['message_count']} messages. "
            . "Configure LLM_API_KEY in .env to enable AI-structured summaries, key insights, and action items.";

        return [
            'report_title' => 'Simulation Debrief Report (Sample)',
            'simulation_name' => $thread['simulation_name'],
            'thread_name' => $thread['thread_name'],
            'generated_at' => Carbon::now()->toIso8601String(),
            'executive_summary' => $summary,
            'key_insights' => [
                'Chat history captured successfully for export.',
                'Enable LLM_API_KEY for AI-generated insights from the full transcript.',
            ],
            'action_items' => [
                ['task' => 'Review transcript appendix with stakeholders', 'owner' => 'TBD', 'priority' => 'medium', 'due' => 'TBD'],
            ],
            'participants' => array_keys($participants),
            'topics_discussed' => ['Simulation dialogue (see transcript preview in appendix)'],
            'decisions_made' => [],
            'risks_and_concerns' => [],
            'recommendations' => ['Configure OpenAI-compatible API credentials for structured AI reports.'],
            'notable_quotes' => [],
            'appendix_notes' => "Transcript preview:\n" . $preview,
            'message_count' => $thread['message_count'],
        ];
    }

    protected function mergeThreadReports(array $threadReports)
    {
        if (count($threadReports) === 1) {
            return [
                'combined' => false,
                'report' => $threadReports[0],
                'sections' => $threadReports,
            ];
        }

        $simulationNames = array_unique(array_column($threadReports, 'simulation_name'));

        return [
            'combined' => true,
            'report' => [
                'report_title' => 'Combined Simulation Debrief Report',
                'simulation_name' => implode(', ', $simulationNames),
                'thread_name' => count($threadReports) . ' selected threads',
                'generated_at' => Carbon::now()->toIso8601String(),
                'executive_summary' => 'This report combines ' . count($threadReports) . ' chat threads. See per-thread sections below.',
                'key_insights' => [],
                'action_items' => [],
                'participants' => [],
                'topics_discussed' => [],
                'decisions_made' => [],
                'risks_and_concerns' => [],
                'recommendations' => [],
                'notable_quotes' => [],
                'appendix_notes' => '',
                'message_count' => array_sum(array_column($threadReports, 'message_count')),
            ],
            'sections' => $threadReports,
        ];
    }

    protected function emptyReport($message)
    {
        return [
            'combined' => false,
            'report' => [
                'report_title' => 'Simulation Debrief Report',
                'simulation_name' => 'N/A',
                'thread_name' => 'N/A',
                'generated_at' => Carbon::now()->toIso8601String(),
                'executive_summary' => $message,
                'key_insights' => [],
                'action_items' => [],
                'participants' => [],
                'topics_discussed' => [],
                'decisions_made' => [],
                'risks_and_concerns' => [],
                'recommendations' => [],
                'notable_quotes' => [],
                'appendix_notes' => '',
                'message_count' => 0,
            ],
            'sections' => [],
        ];
    }

    protected function ensureList($value)
    {
        if (!is_array($value)) {
            return $value ? [(string) $value] : [];
        }
        return array_values(array_filter(array_map('strval', $value)));
    }

    protected function normalizeActionItems($items)
    {
        if (!is_array($items)) {
            return [];
        }
        $normalized = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $normalized[] = ['task' => $item, 'owner' => 'TBD', 'priority' => 'medium', 'due' => 'TBD'];
                continue;
            }
            if (is_array($item)) {
                $normalized[] = [
                    'task' => $item['task'] ?? 'Unnamed task',
                    'owner' => $item['owner'] ?? 'TBD',
                    'priority' => $item['priority'] ?? 'medium',
                    'due' => $item['due'] ?? 'TBD',
                ];
            }
        }
        return $normalized;
    }
}
