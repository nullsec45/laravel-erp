<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
