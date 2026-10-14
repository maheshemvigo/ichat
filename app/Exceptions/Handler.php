<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use PostHog\PostHog;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        $this->reportExceptionToPostHog($exception);

        parent::report($exception);
    }

    /**
     * Send exception context to PostHog (Laravel 5.6 — no bootstrap/app.php withExceptions).
     *
     * @param Exception $exception
     * @return void
     */
    protected function reportExceptionToPostHog(Exception $exception)
    {
        if (!config('services.posthog.enabled', false) || !config('services.posthog.api_key')) {
            return;
        }

        try {
            $distinctId = auth()->check() ? (string) auth()->id() : null;
            if ($distinctId === null || $distinctId === '') {
                $distinctId = 'anonymous';
            }

            $properties = [
                '$exception_message' => $exception->getMessage(),
                '$exception_type' => get_class($exception),
                '$exception_file' => $exception->getFile(),
                '$exception_line' => $exception->getLine(),
            ];

            if (!app()->runningInConsole() && app()->bound('request')) {
                $request = request();
                $properties['$current_url'] = $request->fullUrl();
                $properties['$request_method'] = $request->method();
            }

            PostHog::capture([
                'distinctId' => $distinctId,
                'event' => '$exception',
                'properties' => $properties,
            ]);
            PostHog::flush();
        } catch (Exception $postHogException) {
            // Do not recurse into reporting if PostHog fails.
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof TokenMismatchException) {

            return redirect('/login');

        }
        return parent::render($request, $exception);
    }
}
