<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmploymentHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_name',
        'position',
        'industry',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
