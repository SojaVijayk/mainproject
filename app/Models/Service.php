<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'service';

    protected $fillable = [
        'p_id',
        'department',
        'employment_type',
        'role',
        'pay_type',
        'start_date',
        'end_date',
        'status',
        'consolidated_pay',
    ];

    public function employee()
    {
        return $this->belongsTo(ProjectEmployee::class, 'p_id', 'p_id');
    }
}
