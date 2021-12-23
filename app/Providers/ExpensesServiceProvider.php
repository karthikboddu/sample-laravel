<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ExpensesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'ExpenseService',
            function ( $app ) {
                    return new \App\Services\ExpenseService();
            }
        );

        $this->app->booting(
            function() {
                $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                $loader->alias( 'ExpenseService', 'App\Services\ExpenseService' );
            }
        );            
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
