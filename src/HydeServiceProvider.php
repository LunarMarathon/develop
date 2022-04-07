<?php

namespace Hyde\Framework;

use Composer\InstalledVersions;
use Hyde\Framework\Actions\CreatesDefaultDirectories;
use Hyde\Framework\Actions\PublishesDefaultFrontendResourceFiles;
use Illuminate\Support\ServiceProvider;

class HydeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'hyde.version',
            function () {
                return InstalledVersions::getPrettyVersion('hyde/hyde') ?: 'unreleased';
            }
        );

        $this->app->bind(
            'framework.version',
            function () {
                return InstalledVersions::getPrettyVersion('hyde/framework') ?: 'unreleased';
            }
        );

        $this->commands([
            Commands\HydePublishHomepageCommand::class,
            Commands\HydePublishConfigsCommand::class,
            Commands\HydePublishViewsCommand::class,
            Commands\HydeRebuildStaticSiteCommand::class,
            Commands\HydeBuildStaticSiteCommand::class,
            Commands\HydeMakeValidatorCommand::class,
            Commands\HydePublishStubsCommand::class,
            Commands\HydeMakePostCommand::class,
            Commands\HydeMakePageCommand::class,
            Commands\HydeValidateCommand::class,
            Commands\HydeDebugCommand::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        (new CreatesDefaultDirectories)->__invoke();
        (new PublishesDefaultFrontendResourceFiles())->__invoke();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'hyde');

        $this->publishes([
            __DIR__.'/../config' => config_path(),
        ], 'configs');

        $this->publishes([
            __DIR__.'/../resources/views/layouts' => resource_path('views/vendor/hyde/layouts'),
        ], 'hyde-layouts');

        $this->publishes([
            __DIR__.'/../resources/views/components' => resource_path('views/vendor/hyde/components'),
        ], 'hyde-components');

        $this->publishes([
            __DIR__.'/../resources/views/pages/404.blade.php' => resource_path('views/pages/404.blade.php'),
        ], 'hyde-page-404');
    }
}
