<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\SalesTarget;
use App\Policies\SalesTargetPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        SalesTarget::class => SalesTargetPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for admin access
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manager', function ($user) {
            return $user->isManager();
        });
    }
} 