<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'support_email', 'support_phone', 'support_url'
    ];

    public function models()
    {
        return $this->hasMany(AssetModel::class);
    }
}
