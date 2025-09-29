<?php

namespace App\Models\PMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectExpenseComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'expense_category_id',
        'component',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}