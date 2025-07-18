<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumable_id', 'quantity', 'user_id',
        'department_id', 'floor', 'notes'
    ];

    public function consumable()
    {
        return $this->belongsTo(Consumable::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
