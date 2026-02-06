<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasDepartmentIsolation;

class EvaluationResponse extends Model
{
    use HasFactory, HasDepartmentIsolation;

    protected $fillable = ['form_id', 'user_id', 'department_name'];

    public function form()
    {
        return $this->belongsTo(EvaluationForm::class, 'form_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(EvaluationAnswer::class, 'response_id');
    }
}
