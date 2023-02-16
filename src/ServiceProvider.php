<?php

declare(strict_types=1);

namespace BBSLab\SalesforceEmailTransport;

use BBSLab\SalesforceEmailTransport\SalesforceEmailTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        Mail::extend('salesforce', function (array $config = []) {
            return new SalesforceEmailTransport();
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/salesforce-email-transport.php', 'salesforce-email-transport');

        $this->publishes(
            [
                __DIR__ . '/../config' => config_path(),
            ],
            'salesforce-email-transport-config'
        );
    }
}
