<?php

namespace WalkerChiu\Friendship;

use Illuminate\Support\ServiceProvider;

class FriendshipServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/friendship.php' => config_path('wk-friendship.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_friendship_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_friendship_table.php'
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-friendship');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-friendship'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-friendship.command.cleaner')
            ]);
        }

        config('wk-core.class.friendship.friendship')::observe(config('wk-core.class.friendship.friendshipObserver'));

        $this->bladeDirectives();
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    private function bladeDirectives()
    {
        if (!class_exists('\Blade'))
            return;

        \Blade::directive('friendshipAccept', function ($expression) {
            return "<?php
                if (
                    \\Auth::check()
                    && \\Auth::user()->hasFriendship({$expression}, 'accept')) : ?>";
        });
        \Blade::directive('endfriendshipAccept', function ($expression) {
            return "<?php endif; ?>";
        });

        \Blade::directive('friendshipBlock', function ($expression) {
            return "<?php
                if (
                    \\Auth::check()
                    && \\Auth::user()->hasFriendship({$expression}, 'block')) : ?>";
        });
        \Blade::directive('endfriendshipBlock', function ($expression) {
            return "<?php endif; ?>";
        });
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-friendship')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/friendship.php', 'wk-friendship'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/friendship.php', 'friendship'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
