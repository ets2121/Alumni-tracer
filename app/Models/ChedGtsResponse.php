<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChedGtsResponse extends Model
{
    protected $fillable = [
        'user_id',
        'department_name',
        'response_data',
    ];

    protected $casts = [
        'response_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
