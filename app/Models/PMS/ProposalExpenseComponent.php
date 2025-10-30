<?php

namespace App\Models\PMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProposalExpenseComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
         'proposal_id',
    'expense_category_id',
    'group_name',
    'component',
    'mandays',
    'rate',
    'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate' => 'decimal:2'
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}