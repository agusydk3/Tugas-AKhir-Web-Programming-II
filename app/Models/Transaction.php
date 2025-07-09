<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'user_id',
        'product_id',
        'quantity',
        'total_amount',
        'payment_method',
        'status',
        'expired_at',
        'receipt_number',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expired_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];
    
    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaction) {
            // Generate unique transaction ID if not set
            if (!$transaction->transaction_id) {
                $transaction->transaction_id = 'TRX-' . strtoupper(Str::random(10));
            }
        });
    }
    
    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the product associated with the transaction.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the stocks associated with the transaction.
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
    
    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
    
    /**
     * Format total amount as currency
     *
     * @return string
     */
    public function getFormattedTotal()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
    
    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    /**
     * Scope a query to only include failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    /**
     * Scope a query to only include cancelled transactions.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
