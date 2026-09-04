<?php

namespace App\Models;

use App\Observers\ScholarObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Fillable(['first_name', 'middle_name', 'last_name', 'generational_suffix', 'year_of_award', 'scholarship_id', 'scholarship_type_id', 'spas_no', 'sex', 'birthdate', 'contact_number', 'email_address', 'school_id', 'course_id', 'barangay_id', 'municipality_id', 'province_id', 'region_id', 'clearance_status_id', 'clearance_date', 'for_disposal', 'fts_search_data'])]
#[ObservedBy(ScholarObserver::class)]
class Scholar extends Model
{
    protected $casts = [
        'birthdate' => 'date:Y-m-d',
        'clearance_date' => 'date:Y-m-d',
        'for_disposal' => 'boolean',
    ];

    public $timestamps = false;

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, 'scholarship_id');
    }

    public function scholarshipProgram()
    {
        return $this->belongsTo(Scholarship::class, 'scholarship_id');
    }

    public function scholarshipType()
    {
        return $this->belongsTo(ScholarshipType::class, 'scholarship_type_id');
    }

    public function scholarshipProgramType()
    {
        return $this->belongsTo(ScholarshipType::class, 'scholarship_type_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function clearanceStatus()
    {
        return $this->belongsTo(ClearanceStatus::class, 'clearance_status_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    public function getScholarData(): Scholar
    {
        $scholar = Scholar::with('documents', 'documents.versions', 'documents.versions.fileType')->findOrFail($this->id);

        return $scholar;
    }

    public function getSchoolAndCampus(): string
    {
        $campus = $this->school->campus ? " (" . $this->school->campus : "";
        if (!empty($campus) && !Str::contains($campus, ' Campus')) {
            $campus .= " Campus)";
        }

        $school = $this->school ? $this->school->name . " " . $campus : 'N/A';
        return $school;
    }

    public function getFullName(): string
    {
        return trim("{$this->last_name}, {$this->first_name} {$this->middle_name} {$this->suffix}");
    }

    public function getScholarGroupedDocuments(): array
    {
        // Only get the information needed.
        $documents = DB::select("
            WITH RankedVersions AS (
                SELECT 
                    document_uuid,
                    file_type_id,
                    original_filename as file_name,
                    file_path,
                    created_at as uploaded_at,
                    version_number,
                    mime_type,
                    ROW_NUMBER() OVER(PARTITION BY document_uuid ORDER BY version_number DESC) as rn
                FROM document_versions
            )
            SELECT 
                rv.file_name,
                ft.name as file_type_name,
                d.status,
                d.metadata,
                d.created_at,
                rv.uploaded_at as updated_at,
                rv.version_number,

                -- Columns for the internal query
                rv.file_path,
                rv.mime_type
            FROM documents AS d
            INNER JOIN RankedVersions AS rv ON d.uuid = rv.document_uuid AND rv.rn = 1
            INNER JOIN file_types AS ft 
                ON rv.file_type_id = ft.id
            WHERE d.documentable_type = :documentableType
                AND d.documentable_id = :scholarId
                AND d.deleted_at IS NULL
            ORDER BY ft.name ASC, rv.file_name ASC;
        ", [
            "documentableType" => "App\\Models\\Scholar", 
            "scholarId" => $this->id
        ]);

        $filesArray = json_decode(json_encode($documents), true);
        $file_groups = collect($filesArray)->groupBy('file_type_name')->toArray();
        return $file_groups;
    }

    public function getScholarProfileAsJSON(): string
    {
        $fullName = $this->getFullName();
        $school = $this->getSchoolAndCampus();

        $data = [
            'basic_information' => [
                'full_name'   => $fullName,
                'last_name'   => $this->last_name ?? null,
                'first_name'  => $this->first_name ?? null,
                'middle_name' => $this->middle_name ?? null,
                'suffix'      => $this->suffix ?: null,
            ],
            'scholarship_details' => [
                'spas_number'         => $this->spas_id ?? null,
                'scholarship'         => $this->scholarship->name ?? null,
                'program_type'        => $this->scholarshipType->name ?? null,
                'year_of_award'       => $this->year_of_award ?? null,
                'university_school'   => $school ?: null,
                'degree_program'      => $this->course->name ?? null,
                'clearance_status'    => $this->clearanceStatus->name ?? 'Not Cleared',
                'clearance_date'      => $this->clearance_date 
                    ? \Carbon\Carbon::parse($this->clearance_date)->format('m/d/Y') 
                    : null,
            ],
            'demographic_and_contact_information' => [
                'email_address'  => $this->email_address ?? null,
                'contact_number' => $this->contact_number ?? null,
                'birthdate'      => $this->birthdate 
                    ? \Carbon\Carbon::parse($this->birthdate)->format('m/d/Y') 
                    : null,
                'sex'            => $this->sex ?? null,
                'region'         => isset($this->region) 
                    ? "{$this->region->name} ({$this->region->abbreviation})" 
                    : null,
                'province'            => $this->province->name ?? null,
                'municipality_city'   => $this->municipality->name ?? null,
                'barangay_address'    => $this->barangay->name ?? null,
                'street_lot'          => $this->home_address ?: null,
            ],
            'metadata' => [
                'exported_at' => now()->toIso8601String(),
                'note'        => 'Scholars on and before the 2010s are prone to have missing entries due to the type of data requested back then.',
            ],
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    public function getScholarProfileAsTXT(): string
    {
        $fullName = $this->getFullName();
        $school = $this->getSchoolAndCampus();

        return implode("\n", [
            "======================================================================",
            "                       SCHOLAR PROFILE SUMMARY                        ",
            "======================================================================",
            "",
            "[ BASIC INFORMATION ]",
            "----------------------------------------------------------------------",
            str_pad("Full Name:", 21) . $fullName,
            str_pad("Last Name:", 21) . ($this->last_name ?? 'N/A'),
            str_pad("First Name:", 21) . ($this->first_name ?? 'N/A'),
            str_pad("Middle Name:", 21) . ($this->middle_name ?? 'N/A'),
            str_pad("Suffix:", 21) . ($this->suffix ?: 'N/A'),
            "",
            "[ SCHOLARSHIP DETAILS ]",
            "----------------------------------------------------------------------",
            str_pad("SPAS Number:", 21) . ($this->spas_id ?? 'N/A'),
            str_pad("Scholarship", 21) . ($this->scholarship->name ?? 'N/A'),
            str_pad("Program Type:", 21) . ($this->scholarshipType->name ?? 'N/A'),
            str_pad("Year of Award:", 21) . ($this->year_of_award ?? 'N/A'),
            str_pad("University / School:", 21) . ($school),
            str_pad("Degree Program:", 21) . ($this->course->name ?? 'N/A'),
            str_pad("Clearance Status:", 21) . ($this->clearanceStatus->name ?? 'Not Cleared'),
            str_pad("Clearance Date:", 21) . ($this->clearance_date ? \Carbon\Carbon::parse($this->clearance_date)->format('m/d/Y') : 'N/A'),
            "",
            "[ DEMOGRAPHIC & CONTACT INFORMATION ]",
            "----------------------------------------------------------------------",
            str_pad("Email Address:", 21) . ($this->email_address ?? 'N/A'),
            str_pad("Contact Number:", 21) . ($this->contact_number ?? 'N/A'),
            str_pad("Birthdate:", 21) . ($this->birthdate ? \Carbon\Carbon::parse($this->birthdate)->format('m/d/Y') : 'N/A'),
            str_pad("Sex:", 21) . ($this->sex ?? 'N/A'),
            str_pad("Region:", 21) . ($this->region->name . "(" . $this->region->abbreviation . ")" ?? 'N/A'),
            str_pad("Province:", 21) . ($this->province->name ?? 'N/A'),
            str_pad("Municipality / City:", 21) . ($this->municipality->name ?? 'N/A'),
            str_pad("Barangay / Address:", 21) . ($this->barangay->name ?? 'N/A'),
            str_pad("Street / Lot:", 21) . ($this->home_address ?: 'N/A'),
            "",
            "======================================================================",
            str_pad("Exported At:", 21) . now()->format('Y-m-d H:i:s T'),
            "======================================================================",
            "",
            "Note: Scholars on and before the 2010s are prone to have missing entries due to the type of data requested back then."
        ]);
    }
}
