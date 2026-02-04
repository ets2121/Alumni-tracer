<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Traits\HasDepartmentIsolation;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasDepartmentIsolation;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'avatar',
        'department_name',
    ];

    public function isSystemAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDepartmentAdmin()
    {
        return $this->role === 'dept_admin';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'dept_admin']);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function alumniProfile()
    {
        return $this->hasOne(AlumniProfile::class);
    }

    public function chatGroups()
    {
        return $this->belongsToMany(ChatGroup::class, 'chat_group_user')
            ->withPivot('role', 'last_read_at')
            ->withTimestamps();
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function employmentHistories()
    {
        return $this->hasMany(EmploymentHistory::class)->orderBy('start_date', 'desc');
    }

    public function latestEmployment()
    {
        return $this->hasOne(EmploymentHistory::class)->ofMany([
            'start_date' => 'max',
            'id' => 'max',
        ], function ($query) {
            $query->where('is_current', true)
                ->orWhere('end_date', '>=', now())
                ->orWhereNull('end_date');
        });
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\CustomPasswordReset($token));
    }
}
