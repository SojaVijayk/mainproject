<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_tag', 'name', 'description', 'model_id', 'status_id',
        'supplier_id', 'location_id', 'purchase_cost', 'purchase_date',
        'warranty_expiry', 'serial_number', 'notes', 'assigned_to',
        'assigned_type', 'department_id', 'floor'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    public function model()
    {
        return $this->belongsTo(AssetModel::class);
    }

    public function status()
    {
        return $this->belongsTo(AssetStatus::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function maintenance()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function history()
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function getAssignedToAttribute()
    {
        if ($this->assigned_type === 'user' && $this->assignedUser) {
            return $this->assignedUser->name;
        } elseif ($this->assigned_type === 'department' && $this->department) {
            return $this->department->name . ' (Department)';
        }
        return 'Unassigned';
    }

    public function generateBarcode()
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        return 'data:image/png;base64,' . base64_encode($generator->getBarcode(
            $this->asset_tag,
            $generator::TYPE_CODE_128
        ));
    }
}
