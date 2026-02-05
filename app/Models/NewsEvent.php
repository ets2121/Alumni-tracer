<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasDepartmentIsolation;

class NewsEvent extends Model
{
    use HasDepartmentIsolation;
    protected $fillable = [
        'title',
        'slug',
        'content',
        'type',
        'visibility_type',
        'image_path',
        'event_date',
        'location',
        'job_company',
        'job_location',
        'job_salary',
        'job_link',
        'job_deadline',
        'author',
        'category',
        'registration_link',
        'is_pinned',
        'expires_at',
        'target_type',
        'target_batch',
        'target_course_id',
        'department_name',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'expires_at' => 'datetime',
        'job_deadline' => 'datetime',
        'category' => 'array',
        'is_pinned' => 'boolean',
        'target_course_id' => 'integer',
    ];

    public function targetCourse()
    {
        return $this->belongsTo(Course::class, 'target_course_id');
    }

    public function photos()
    {
        return $this->hasMany(NewsEventPhoto::class);
    }

    public function reactions()
    {
        return $this->hasMany(NewsEventReaction::class);
    }

    public function comments()
    {
        return $this->hasMany(NewsEventComment::class);
    }

    public function userReaction()
    {
        return $this->hasOne(NewsEventReaction::class)->where('user_id', auth()->id());
    }
}
