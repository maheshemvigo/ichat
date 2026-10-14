<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SimulationReportStorageService
{
    /**
     * Chat history report (no LLM) — upload to S3.
     *
     * @param int $simulationId
     * @param array $payload from SimulationChatHistoryReportService
     * @return string S3 object key
     */
    public function storeChatHistoryReport($simulationId, array $payload)
    {
        $report_data = $payload['report_data'];
        $simulation_name = $payload['simulation_name'];
        $generated_at = $payload['generated_at'];

        $html = view('simulation.reports.chat_history_report_document', compact(
            'report_data',
            'simulation_name',
            'generated_at'
        ))->render();

        $prefix = rtrim(config('llm_report.s3_prefix', 'simulation-history-reports'), '/');
        $filename = 'simulation-' . $simulationId . '-history-' . date('Ymd-His') . '.doc';
        $key = $prefix . '/' . $simulationId . '/' . $filename;

        $disk = Storage::disk(config('llm_report.storage_disk', 's3'));
        $disk->put($key, $html, 'private');

        return $key;
    }

    public function storeStructuredReport($simulationId, array $payload)
    {
        $html = view('simulation.reports.structured_report_document', compact('payload'))->render();

        $prefix = rtrim(config('llm_report.s3_prefix', 'simulation-history-reports'), '/');
        $filename = 'simulation-' . $simulationId . '-summary-' . date('Ymd-His') . '.doc';
        $key = $prefix . '/' . $simulationId . '/' . $filename;

        $disk = Storage::disk(config('llm_report.storage_disk', 's3'));

        $disk->put($key, $html, 'private');

        return $key;
    }

    /**
     * Pre-signed URL for downloading the report from the app.
     *
     * @param string $s3Key
     * @return string
     */
    public function temporaryDownloadUrl($s3Key)
    {
        $minutes = (int) config('llm_report.signed_url_minutes', 60);
        $disk = Storage::disk(config('llm_report.storage_disk', 's3'));
        $filename = basename($s3Key);

        return $disk->temporaryUrl(
            $s3Key,
            Carbon::now()->addMinutes($minutes),
            [
                'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
                'ResponseContentType' => 'application/octet-stream',
            ]
        );
    }
}
