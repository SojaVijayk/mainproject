<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category_id', 'supplier_id', 'quantity',
        'min_quantity', 'purchase_cost', 'purchase_date'
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function assignments()
    {
        return $this->hasMany(ConsumableAssignment::class);
    }

    public function getAvailableQuantityAttribute()
    {
        return $this->quantity - $this->assignments()->sum('quantity');
    }
}
