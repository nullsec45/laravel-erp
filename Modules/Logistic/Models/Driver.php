<?php

namespace Modules\Logistic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\HR\Models\Employee;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'license_number',
        'license_type',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
