<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'gender',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'password_plain',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Persist bcrypt in `password` and the submitted plaintext in `password_plain` (also hidden from JSON).
     */
    public function setPasswordAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['password'] = $value;
            $this->attributes['password_plain'] = null;

            return;
        }

        if ($this->passwordValueLooksHashed($value)) {
            $this->attributes['password'] = $value;
            $this->attributes['password_plain'] = null;

            return;
        }

        $this->attributes['password'] = Hash::make($value);
        $this->attributes['password_plain'] = $value;
    }

    private function passwordValueLooksHashed(string $value): bool
    {
        return strlen($value) === 60 && preg_match('/^\$2[aby]\$/', $value) === 1;
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function monthlyPlannerEntries()
    {
        return $this->hasMany(MonthlyPlannerEntry::class);
    }
}
