<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyPlannerEntry extends Model
{
    protected $fillable = [
        'user_id',
        'month_label',
        'year',
        'salary',
        'cash_on_hand',
        'total_expenses',
        'total_cash',
        'remaining',
        'expenses',
    ];

    protected $casts = [
        'year' => 'integer',
        'salary' => 'decimal:2',
        'cash_on_hand' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'total_cash' => 'decimal:2',
        'remaining' => 'decimal:2',
        'expenses' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
