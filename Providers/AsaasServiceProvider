<?php

namespace Asaas\Providers;

use Asaas\AsaasClient;
use Asaas\Customers;
use Asaas\PaymentLinks;
use Illuminate\Support\ServiceProvider;

class AsaasServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/asaas.php', 'asaas');

        $this->app->singleton(AsaasClient::class, function ($app) {
            return new AsaasClient($app['config']['asaas']);
        });

        $this->app->singleton('asaas', function ($app) {
            return new class($app[AsaasClient::class]) {
                public function __construct(
                    protected AsaasClient $client
                ) {}

                public function customers(): Customers
                {
                    return new Customers($this->client);
                }

                public function paymentLinks(): PaymentLinks
                {
                    return new PaymentLinks($this->client);
                }
            };
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/asaas.php' => config_path('asaas.php'),
            ], 'asaas-config');
        }
    }
}
