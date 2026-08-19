<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Scholar;
use Illuminate\Support\Facades\Auth;

class ScholarObserver
{

    public bool $afterCommit = true;
    /**
     * Handle the Scholar "created" event.
     */
    public function created(Scholar $scholar): void
    {
        $userId = Auth::id() ?? \App\Models\User::query()->value('id') ?? 1;
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'created',
            'record_type' => Scholar::class,
            'record_id' => $scholar->id,
            'before_payload' => null,
            'after_payload' => $scholar->toArray(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the Scholar "updated" event.
     */
    public function updated(Scholar $scholar): void
    {
        if ($scholar->wasChanged()) {
            $userId = Auth::id() ?? \App\Models\User::query()->value('id') ?? 1;
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'updated',
                'record_type' => Scholar::class,
                'record_id' => $scholar->id,
                'before_payload' => $scholar->getOriginal(),
                'after_payload' => $scholar->getChanges(),
                'ip_address' => request()->ip(),
            ]);
        }
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

    /**
     * Handle the Scholar "deleted" event.
     */
    public function deleted(Scholar $scholar): void
    {
        $userId = Auth::id() ?? \App\Models\User::query()->value('id') ?? 1;
        AuditLog::create([
            'user_id' => $userId,
            'action' => 'deleted',
            'record_type' => Scholar::class,
            'record_id' => $scholar->id,
            'before_payload' => $scholar->toArray(),
            'after_payload' => null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle the Scholar "restored" event.
     */
    public function restored(Scholar $scholar): void
    {
        //
    }

    /**
     * Handle the Scholar "force deleted" event.
     */
    public function forceDeleted(Scholar $scholar): void
    {
        //
    }
}
