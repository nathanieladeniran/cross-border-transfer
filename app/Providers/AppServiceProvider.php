<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Profile;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Relation::morphMap([
            //'profile' => Profile::class,
            'profile' => User::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
