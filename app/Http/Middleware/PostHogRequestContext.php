<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use PostHog\PostHog;
use Symfony\Component\HttpFoundation\Response;

class PostHogRequestContext
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->isEnabled()) {
            return $next($request);
        }

        $distinctId = $this->resolveDistinctId($request);
        $request->attributes->set('posthog_distinct_id', $distinctId);

        if (Auth::check()) {
            $user = Auth::user();
            try {
                PostHog::identify([
                    'distinctId' => $distinctId,
                    'properties' => [
                        'name' => $user->name,
                        'account_type' => (int) $user->type === 1 ? 'admin' : 'participant',
                        'admin_type' => isset($user->admin_type) ? $user->admin_type : null,
                    ],
                ]);
            } catch (\Exception $e) {
                report($e);
            }
        }

        return $next($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function terminate($request, $response)
    {
        if (!$this->isEnabled()) {
            return;
        }

        if (!$request instanceof Request) {
            return;
        }

        $distinctId = $request->attributes->get('posthog_distinct_id');
        if (empty($distinctId)) {
            $distinctId = $this->resolveDistinctId($request);
        }

        if ($this->shouldSkipRequest($request)) {
            return;
        }

        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        try {
            PostHog::capture([
                'distinctId' => $distinctId,
                'event' => '$pageview',
                'properties' => [
                    '$current_url' => $request->fullUrl(),
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'route_name' => $routeName,
                    'status_code' => method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null,
                    'is_authenticated' => Auth::check(),
                ],
            ]);
            PostHog::flush();
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * @return bool
     */
    protected function isEnabled()
    {
        if (!config('services.posthog.enabled', false)) {
            return false;
        }

        return !empty(config('services.posthog.api_key'));
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function resolveDistinctId(Request $request)
    {
        if (Auth::check()) {
            return 'user_' . Auth::id();
        }

        if ($request->hasSession()) {
            if (!$request->session()->has('posthog_distinct_id')) {
                $request->session()->put('posthog_distinct_id', 'anon_' . str_random(32));
            }

            return (string) $request->session()->get('posthog_distinct_id');
        }

        return 'anon_' . md5($request->ip() . '|' . $request->userAgent());
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function shouldSkipRequest(Request $request)
    {
        if ($request->is('_debugbar/*')) {
            return true;
        }

        $path = $request->path();
        if (in_array($path, ['favicon.ico', 'robots.txt'], true)) {
            return true;
        }

        return false;
    }
}
