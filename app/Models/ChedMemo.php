<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChedMemo extends Model
{
    protected $fillable = ['title', 'memo_number', 'file_path', 'date_issued', 'description', 'category'];

    protected $casts = [
        'date_issued' => 'date',
    ];
    //
}
