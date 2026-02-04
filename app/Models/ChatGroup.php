<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\HasDepartmentIsolation;

class ChatGroup extends Model
{
    use HasFactory, HasDepartmentIsolation;

    const TYPE_ADMIN_DEPT = 'admin_dept';
    const TYPE_GENERAL = 'general';
    const TYPE_BATCH = 'batch';
    const TYPE_COURSE = 'course';

    protected $fillable = [
        'name',
        'type',
        'batch_year',
        'course_id',
        'department_name',
        'description',
        'cover_image',
        'is_private',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_group_user')
            ->withPivot('role', 'last_read_at')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }
}
