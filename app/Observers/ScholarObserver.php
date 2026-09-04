<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Scholar;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Ports ScholarObserver from db-integration (Q08=C) onto mother audit_logs.record_*.
 * No fts_search_data / folders / DocumentObserver in this slice.
 */
class ScholarObserver
{
    public bool $afterCommit = true;

    /**
     * Handle the Scholar "created" event.
     */
    public function created(Scholar $scholar): void
    {
        $this->writeAudit('created', $scholar, before: null, after: $scholar->toArray());
    }

    public function updated(Scholar $scholar): void
    {
        if (!$scholar->wasChanged()) {
            return;
        }

        $this->writeAudit(
            'updated',
            $scholar,
            before: $scholar->getOriginal(),
            after: $scholar->getChanges(),
        );
    }

    public function saving(Scholar $scholar)
    {
        // Prepares the full text search column

        $scholar->loadMissing(['school', 'course', 'scholarshipProgram', 'scholarshipProgramType', 'clearanceStatus']);

        // Compile the data into a single searchable string
        $searchData = implode(' ', array_filter([
            $scholar->first_name,
            $scholar->middle_name,
            $scholar->last_name,
            $scholar->school?->name,
            $scholar->course?->name,
            $scholar->course?->abbreviation,
            $scholar->scholarship_program?->name,           // e.g., "DOST-SEI RA 10612"
            $scholar->scholarship_program_type?->name,      // e.g., "Undergraduate Schola rship"
            $scholar->year_of_award,
            $scholar->spas_number,
            $scholar->contact_number,
            $scholar->email_address,
            $scholar->clearance_status?->name,      // e.g., "Cleared"
            $scholar->clearance_date,
        ]));

        $scholar->fts_search_data = $searchData;
    }
  
    public function deleted(Scholar $scholar): void
    {
        $this->writeAudit('deleted', $scholar, before: $scholar->toArray(), after: null);
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    protected function writeAudit(string $action, Scholar $scholar, ?array $before, ?array $after): void
    {
        $userId = Auth::id();
        if (! $userId) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => $userId,
            'action' => $action,
            'record_type' => Scholar::class,
            'record_id' => $scholar->id,
            'before_payload' => $before,
            'after_payload' => $after,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
