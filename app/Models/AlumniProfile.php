<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

use App\Traits\HasDepartmentIsolation;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AlumniProfile extends Model
{
    use HasFactory, HasDepartmentIsolation;

    protected static function booted()
    {
        static::saving(function ($profile) {
            if ($profile->course_id) {
                $course = Course::find($profile->course_id);
                if ($course) {
                    $profile->department_name = $course->department_name;

                    // Sync to parent User record for indexed performance filtering
                    if ($profile->user_id) {
                        \App\Models\User::withoutGlobalScopes()
                            ->where('id', $profile->user_id)
                            ->update(['department_name' => $course->department_name]);
                    }
                }
            }
        });
    }

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'dob',
        'civil_status',
        'contact_number',
        'address',
        'course_id',
        'batch_year',
        'employment_status',
        'field_of_work',
        'work_status',
        'establishment_type',
        'work_location',
        'company_name',
        'position',
        'work_address',
        'proof_path',
        'department_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
