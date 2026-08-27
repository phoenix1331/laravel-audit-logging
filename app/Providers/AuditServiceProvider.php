<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AuditLogger;
use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuditLogger::class, function ($app) {
            return new AuditLogger(
                $app['request'],
                $app['auth']->guard(),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
