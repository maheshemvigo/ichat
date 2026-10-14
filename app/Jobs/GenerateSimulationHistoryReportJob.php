<?php

namespace App\Jobs;

use App\Simulation;
use App\Services\LlmStructuredReportService;
use App\Services\SimulationChatHistoryReportService;
use App\Services\SimulationReportStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateSimulationHistoryReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $simulationId;

    public $tries = 2;

    public $timeout = 3600;

    public function __construct($simulationId)
    {
        $this->simulationId = (int) $simulationId;
    }

    public function handle(
        LlmStructuredReportService $llmReportService,
        SimulationChatHistoryReportService $chatHistoryReportService,
        SimulationReportStorageService $storageService
    ) {
        $simulation = Simulation::find($this->simulationId);

        if (!$simulation) {
            return;
        }

        if ($this->shouldUseLlm()) {
            $payload = $llmReportService->generateForSimulation($this->simulationId);
            $s3Key = $storageService->storeStructuredReport($this->simulationId, $payload);
        } else {
            $payload = $chatHistoryReportService->buildForSimulation($this->simulationId);
            $s3Key = $storageService->storeChatHistoryReport($this->simulationId, $payload);
        }

        Simulation::where('id', $this->simulationId)->update([
            'history_summary_url' => $s3Key,
        ]);
    }

    /**
     * Use OpenAI structured summary when enabled and API key is configured.
     */
    protected function shouldUseLlm()
    {
        return config('llm_report.enabled') && !empty(config('llm_report.api_key'));
    }

    public function failed(\Exception $exception)
    {
        Log::error('Simulation history report job failed', [
            'simulation_id' => $this->simulationId,
            'message' => $exception->getMessage(),
        ]);
    }
}
