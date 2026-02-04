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
        'image_path',
        'event_date',
        'location',
        'author',
        'category',
        'registration_link',
        'is_pinned',
        'expires_at',
        'target_type',
        'target_batch',
        'target_course_id',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'expires_at' => 'datetime',
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
}
