<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tapal extends Model
{
    use HasFactory;

    protected $fillable = [
        'tapal_number',
        'type',
        'inward_date',
        'received_date',
        'inward_mode',
        'mail_id',
        'from_name',
        'from_address',
        'from_department',
        'from_mobile',
        'from_person_details',
        'ref_number',
        'letter_date',
        'subject',
        'description',
        'current_holder_id',
        'created_by'
    ];

    public function attachments()
    {
        return $this->hasMany(TapalAttachment::class);
    }

    public function movements()
    {
        return $this->hasMany(TapalMovement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentHolder()
    {
        return $this->belongsTo(User::class, 'current_holder_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tapal) {
            $year = date('Y');
            $lastTapal = Tapal::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
            $number = $lastTapal ? intval(substr($lastTapal->tapal_number, -4)) + 1 : 1;
            $tapal->tapal_number = 'CMD/IW/' . $year . '/' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
}
