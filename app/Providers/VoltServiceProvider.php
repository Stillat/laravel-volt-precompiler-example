<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

class VoltServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $compiledPath = storage_path('framework/views/compiled_inline_livewire');

        if (! file_exists($compiledPath)) {
            mkdir($compiledPath, 0755, true);
        }

        Volt::mount([
            resource_path('views/livewire'),
            resource_path('views/pages'),
            $compiledPath,
        ]);
    }
}
