<?php

namespace App\Services;

use App\Simulation;
use Auth;
use PostHog\PostHog;

class PostHogAnalyticsService
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        if (!config('services.posthog.enabled', false)) {
            return false;
        }

        return !empty(config('services.posthog.api_key'));
    }

    /**
     * Track a new simulation row in PostHog (Trends → event simulation_created → total count by day).
     *
     * @param Simulation $simulation
     * @param array $properties
     * @param int|null $timestamp Unix timestamp for historical backfill
     * @return void
     */
    public function captureSimulationCreated(Simulation $simulation, array $properties = [], $timestamp = null)
    {
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $this->ensureInitialized();

            $createdAt = $simulation->created_at ? $simulation->created_at : date('Y-m-d H:i:s');
            $distinctId = $this->resolveDistinctIdForSimulation($simulation);

            $message = [
                'distinctId' => $distinctId,
                'event' => 'simulation_created',
                'properties' => array_merge([
                    'simulation_id' => (int) $simulation->id,
                    'simulation_name' => $simulation->simulation_name,
                    'created_by_user_id' => (int) $simulation->created_by,
                    'created_day' => date('Y-m-d', strtotime($createdAt)),
                ], $properties),
            ];

            if ($timestamp !== null) {
                $message['timestamp'] = (int) $timestamp;
            }

            PostHog::capture($message);
            PostHog::flush();
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * @return void
     */
    protected function ensureInitialized()
    {
        PostHog::init(
            config('services.posthog.api_key'),
            [
                'host' => config('services.posthog.host'),
            ]
        );
    }

    /**
     * @param Simulation $simulation
     * @return string
     */
    protected function resolveDistinctIdForSimulation(Simulation $simulation)
    {
        if (Auth::check()) {
            return 'user_' . Auth::id();
        }

        if (!empty($simulation->created_by)) {
            return 'user_' . (int) $simulation->created_by;
        }

        return 'system';
    }
}
