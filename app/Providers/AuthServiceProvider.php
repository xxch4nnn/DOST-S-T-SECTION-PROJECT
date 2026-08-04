<?php

namespace App\Providers;

use App\Models\AdministrativeRecord;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Scholar;
use App\Policies\AdminRecordPolicy;
use App\Policies\AuditLogPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\ScholarPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Scholar::class => ScholarPolicy::class,
        AdministrativeRecord::class => AdminRecordPolicy::class,
        Document::class => DocumentPolicy::class,
        AuditLog::class => AuditLogPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
                return true;
            }

            return null;
        });
    }
}
