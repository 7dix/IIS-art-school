<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // add this statement to public function boot()
        if($this->app->environment('production')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
            
            // Force HTTPS in Mix/Vite asset loading
            if (request()->header('x-forwarded-proto') === 'https') {
                URL::forceScheme('https');
                \Illuminate\Support\Facades\App::setRequestForConsoleEnvironment();
            }
        };

        Inertia::share([
            'auth' => function () {
                /** @var \App\Models\User */
                $user = Auth::user();
                return $user ? [
                    'id' => $user->id,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ] : null;
            },
        ]);


    }
}
