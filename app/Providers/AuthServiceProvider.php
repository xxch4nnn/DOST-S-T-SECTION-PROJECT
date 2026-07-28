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
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\PermissionRegistrar;

class AuthServiceProvider extends ServiceProvider
{
    protected array $policies = [
        Scholar::class                 => ScholarPolicy::class,
        AdministrativeRecord::class    => AdminRecordPolicy::class,
        Document::class                => DocumentPolicy::class,
        AuditLog::class                => AuditLogPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super Admin')) {
                return true;
            }
        });

        // Only seed if baseline is absent; avoids polluting fresh installs.
        if (! app(PermissionRegistrar::class)->permissions->pluck('name')->contains('viewScholars')) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $permissions = [
                'viewAdminRecords',
                'createAdminRecords',
                'editAdminRecords',
                'deleteAdminRecords',
                'viewScholars',
                'createScholars',
                'editScholars',
                'deleteScholars',
            ];

            foreach ($permissions as $name) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}
