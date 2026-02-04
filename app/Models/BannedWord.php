<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BannedWord extends Model
{
    use HasFactory;
    use \App\Traits\HasDepartmentIsolation;

    protected $fillable = ['word', 'department_name', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
