<?php

namespace EmizorIpx\WhatsappMessages;

use Illuminate\Support\ServiceProvider;

class WhatsappMessagesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->loadRoutesFrom(__DIR__."/Routes/api.php");

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // ROUTES
        $this->loadRoutesFrom(__DIR__."/Routes/api.php");

        // MIGRATIONS

        $this->loadMigrationsFrom(__DIR__."/Database/Migrations");

        // VIEWS
        // $this->loadViewsFrom(__DIR__ . "/Resources/Views", "posinvoicingfel");
        // $this->publishes([__DIR__.'/Resources/orders' => resource_path('views/orders/'),]);
        //assets
        // $this->publishes([__DIR__.'/Resources/assets' => public_path('vendor/posinvoicingfel'),__DIR__.'/Resources/Views/orders' => resource_path('views/orders/')], 'public');

        # CONFIG FILE
        // $this->publishes([
        //     __DIR__."/Config/posinvoicingfel.php" => config_path('posinvoicingfel.php')
        // ]);

        // $this->mergeConfigFrom(__DIR__.'/Config/posinvoicingfel.php', 'posinvoicingfel');

        // LOAD COMMANDS

    }
}
