<?php

namespace App\Console\Commands;

use App\Simulation;
use App\Services\PostHogAnalyticsService;
use Illuminate\Console\Command;

class PostHogBackfillSimulationCreated extends Command
{
    /**
     * @var string
     */
    protected $signature = 'posthog:backfill-simulation-created
                            {--days=30 : Only simulations created in the last N days (0 = all)}
                            {--dry-run : Print counts without sending to PostHog}';

    /**
     * @var string
     */
    protected $description = 'Send simulation_created events to PostHog for existing DB rows (historical chart data)';

    /**
     * @param PostHogAnalyticsService $postHogAnalytics
     * @return int
     */
    public function handle(PostHogAnalyticsService $postHogAnalytics)
    {
        if (!$postHogAnalytics->isEnabled()) {
            $this->error('PostHog is disabled or POSTHOG_API_KEY is missing.');

            return 1;
        }

        $days = (int) $this->option('days');
        $dryRun = (bool) $this->option('dry-run');

        $query = Simulation::query()->orderBy('id', 'asc');
        if ($days > 0) {
            $query->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-' . $days . ' days')));
        }

        $total = (clone $query)->count();
        if ($total === 0) {
            $this->info('No simulations matched the filter.');

            return 0;
        }

        $this->info(($dryRun ? '[dry-run] Would send ' : 'Sending ') . $total . ' simulation_created event(s).');

        if ($dryRun) {
            return 0;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunk(50, function ($simulations) use ($postHogAnalytics, $bar) {
            foreach ($simulations as $simulation) {
                $timestamp = $simulation->created_at ? strtotime($simulation->created_at) : time();
                $postHogAnalytics->captureSimulationCreated($simulation, [
                    'source' => 'backfill',
                    'backfill' => true,
                ], $timestamp);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');
        $this->info('Done. Refresh your PostHog insight (may take a minute to ingest).');

        return 0;
    }
}
