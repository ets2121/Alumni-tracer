<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasDepartmentIsolation;

class ChedMemo extends Model
{
    use HasDepartmentIsolation;
    protected $fillable = ['title', 'memo_number', 'file_path', 'date_issued', 'description', 'category'];

    protected $casts = [
        'date_issued' => 'date',
    ];
    //
}
