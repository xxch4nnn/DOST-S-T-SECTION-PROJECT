<?php

namespace App\Models;

use App\Observers\ScholarObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(ScholarObserver::class)]
class Scholar extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'generational_suffix',
        'year_of_award', 'scholarship_id', 'scholarship_type_id', 'spas_no',
        'sex', 'birthdate', 'contact_number', 'email_address', 'school_id',
        'course_id', 'program', 'barangay', 'municipality', 'district',
        'province', 'region_id', 'clearance_status_id', 'clearance_date', 'for_disposal',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'clearance_date' => 'date',
        'for_disposal' => 'boolean',
    ];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function scholarshipType()
    {
        return $this->belongsTo(ScholarshipType::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function clearanceStatus()
    {
        return $this->belongsTo(ClearanceStatus::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
