<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        if (config('database.default') === 'mysql') {
            try {
                \Illuminate\Support\Facades\DB::statement('SET SESSION sql_require_primary_key=0');
            } catch (\Exception $e) {
                // Ignore
            }
        }
    }
}
