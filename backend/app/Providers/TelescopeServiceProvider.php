<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

if (class_exists('Laravel\Telescope\TelescopeApplicationServiceProvider')) {
    class TelescopeServiceProvider extends \Laravel\Telescope\TelescopeApplicationServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            if (!class_exists('Laravel\Telescope\Telescope')) {
                return;
            }

            $this->hideSensitiveRequestDetails();

            $isLocal = $this->app->environment('local');

            \Laravel\Telescope\Telescope::filter(function (\Laravel\Telescope\IncomingEntry $entry) use ($isLocal) {
                return $isLocal ||
                       $entry->isReportableException() ||
                       $entry->isFailedRequest() ||
                       $entry->isFailedJob() ||
                       $entry->isScheduledTask() ||
                       $entry->hasMonitoredTag();
            });
        }

        /**
         * Prevent sensitive request details from being logged by Telescope.
         */
        protected function hideSensitiveRequestDetails(): void
        {
            if (!class_exists('Laravel\Telescope\Telescope') || $this->app->environment('local')) {
                return;
            }

            \Laravel\Telescope\Telescope::hideRequestParameters(['_token']);

            \Laravel\Telescope\Telescope::hideRequestHeaders([
                'cookie',
                'x-csrf-token',
                'x-xsrf-token',
            ]);
        }

        /**
         * Register the Telescope gate.
         *
         * This gate determines who can access Telescope in non-local environments.
         */
        protected function gate(): void
        {
            Gate::define('viewTelescope', fn ($user = null) => app()->environment('local'));
        }
    }
} else {
    class TelescopeServiceProvider extends ServiceProvider
    {
        public function register(): void
        {
            // Telescope not installed, do nothing
        }
    }
}
