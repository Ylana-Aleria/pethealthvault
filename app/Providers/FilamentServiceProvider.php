<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Filament::serving(function () {
            // ✅ Ensure only role_id == 1 can access Filament
            if (Gate::denies('viewFilament')) {
                abort(403);
            }

            Filament::registerUserMenuItems([
                // Optional user menu items here
            ]);
        });
    }
}
