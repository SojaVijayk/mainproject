<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contact_person', 'email', 'phone', 'address'
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function consumables()
    {
        return $this->hasMany(Consumable::class);
    }
}
