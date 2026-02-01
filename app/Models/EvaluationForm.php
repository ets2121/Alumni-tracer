<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationForm extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'type', 'is_active', 'version', 'parent_form_id', 'is_draft'];

    public function questions()
    {
        return $this->hasMany(EvaluationQuestion::class, 'form_id')->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(EvaluationResponse::class, 'form_id');
    }
}
