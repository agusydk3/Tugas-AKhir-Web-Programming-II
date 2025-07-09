<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance' => 'decimal:2',
    ];
    
    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    /**
     * Check if user is reseller
     *
     * @return bool
     */
    public function isReseller()
    {
        return $this->role === 'reseller';
    }
    
    /**
     * Check if user is customer
     *
     * @return bool
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }
    
    /**
     * Get role name with first letter capitalized
     *
     * @return string
     */
    public function getRoleName()
    {
        return ucfirst($this->role);
    }
    
    /**
     * Format balance as currency
     *
     * @return string
     */
    public function getFormattedBalance()
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }
    
    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
