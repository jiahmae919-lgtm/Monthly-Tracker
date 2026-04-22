<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'total_amount',
        'monthly_payment',
        'remaining_balance',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
