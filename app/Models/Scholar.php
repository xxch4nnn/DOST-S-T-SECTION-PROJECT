<?php

namespace App\Models;

use App\Observers\ScholarObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[Fillable(['first_name', 'middle_name', 'last_name', 'generational_suffix', 'year_of_award', 'scholarship_id', 'scholarship_type_id', 'spas_no', 'sex', 'birthdate', 'contact_number', 'email_address', 'school_id', 'course_id', 'barangay', 'municipality', 'district', 'province', 'region_id', 'clearance_status_id', 'clearance_date', 'for_disposal', 'fts_search_data'])]
#[ObservedBy(ScholarObserver::class)]
class Scholar extends Model
{
    protected $casts = [
        'birthdate' => 'date:Y-m-d',
    ];

    public $timestamps=false;
    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, "scholarship_id");
    }

    public function scholarshipProgram()
    {
        return $this->belongsTo(Scholarship::class, "scholarship_id");
    }
    
    public function scholarshipType()
    {
        return $this->belongsTo(ScholarshipType::class, "scholarship_type_id");
    }

    public function scholarshipProgramType()
    {
        return $this->belongsTo(ScholarshipType::class, "scholarship_type_id");
    }
    
    public function school(){
        return $this->belongsTo(School::class, "school_id");
    }
    
    public function course(){
        return $this->belongsTo(Course::class, "course_id");
    }

    public function region(){
        return $this->belongsTo(Region::class, "region_id");
    }

    public function clearanceStatus(){
        return $this->belongsTo(ClearanceStatus::class, "clearance_status_id");
    }

    public function documents(){
        return $this->morphMany(Document::class, 'documentable');
    }
}
