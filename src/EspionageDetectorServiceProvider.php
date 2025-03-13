<?php

namespace 1234nin4321\Seat\EspionageDetector;

use Illuminate\Support\ServiceProvider;

class EspionageDetectorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->addRoutes();
        $this->addViews();
        $this->addMigrations();
        $this->publishConfig();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/espionage-detector.php', 'espionage-detector');
    }

    private function addRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
    }

    private function addViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'espionage-detector');
    }

    private function addMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    private function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/espionage-detector.php' => config_path('espionage-detector.php'),
        ], 'config');
    }
}