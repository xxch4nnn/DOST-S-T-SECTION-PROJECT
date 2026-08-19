<?php

namespace App\Models;

use App\Observers\ScholarObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

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
        return $this->morphMany(Document::class, 'documentable_type');
    }
}
