<?php

namespace App\Providers;
use App\Models\Tapal;
use App\Models\TapalMovement;

use App\Policies\TapalPolicy;
use App\Policies\TapalMovementPolicy;


// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Tapal::class => TapalPolicy::class,
    TapalMovement::class => TapalMovementPolicy::class,
    Booking::class => BookingPolicy::class,
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        //
    }
}
