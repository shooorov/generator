<?php

namespace Shooorov\Generator;

use Illuminate\Support\ServiceProvider;
use Shooorov\Generator\Console\Commands\PublishCommand;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
		$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');

		$this->publishes([
			__DIR__.'/../resources/js' => $this->app->resourcePath('js/Pages/vendor'),
		], ['generator-resources', 'laravel-assets']);

        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishCommand::class,
            ]);
		}
	}


}
