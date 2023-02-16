<?php

declare(strict_types=1);

namespace BBSLab\SalesforceEmailTransport;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        collect(config('mail.mailers'))
            ->filter(fn(array $config) => str_starts_with(data_get($config, 'transport'), 'salesforce'))
            ->each(function(array $config, string $mailer) {
                Mail::extend($mailer, function () use ($config, $mailer) {
                    return new SalesforceEmailTransport(
                        name: $mailer,
                        config: Arr::except($config, ['transport']),
                    );
                });
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
