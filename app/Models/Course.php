<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDepartmentIsolation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasDepartmentIsolation, HasFactory;

    protected $fillable = ['code', 'name', 'department_name', 'description', 'category'];

    /**
     * Set the course code (Trimmed and Uppercase)
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }

    /**
     * Set the course name (Trimmed and Uppercase)
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(trim($value));
    }

    /**
     * Set the department name (Trimmed and Uppercase)
     */
    public function setDepartmentNameAttribute($value)
    {
        $this->attributes['department_name'] = strtoupper(trim($value));
    }

    public function alumni()
    {
        return $this->hasMany(AlumniProfile::class);
    }
}
