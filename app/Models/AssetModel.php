<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category_id', 'manufacturer_id',
        'model_number', 'image', 'is_consumable'
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
