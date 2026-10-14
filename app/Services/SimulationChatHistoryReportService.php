<?php

namespace App\Services;

use App\Simulation;

class SimulationChatHistoryReportService
{
    protected $collector;

    public function __construct(SimulationHistoryCollector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * Build report payload from raw chat messages (no LLM).
     *
     * @param int $simulationId
     * @return array
     */
    public function buildForSimulation($simulationId)
    {
        $simulation = Simulation::find($simulationId);

        if (!$simulation) {
            return [
                'simulation_name' => 'Simulation',
                'generated_at' => date('Y-m-d H:i:s'),
                'report_data' => [],
            ];
        }

        $blocks = $this->collector->collectForSimulation($simulationId);
        $reportData = [];

        foreach ($blocks as $block) {
            $reportData[] = $block['messages'];
        }

        return [
            'simulation_name' => $simulation->simulation_name,
            'generated_at' => date('Y-m-d H:i:s'),
            'report_data' => $reportData,
        ];
    }
}
