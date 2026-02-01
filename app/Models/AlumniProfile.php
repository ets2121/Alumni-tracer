<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

class AlumniProfile extends Model
{
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
        'proof_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
