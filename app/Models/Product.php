<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'notes',
        'price',
        'image',
        'is_active',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the formatted price
     *
     * @return string
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
    
    /**
     * Get the stocks for the product.
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
    
    /**
     * Get the stock count for the product.
     * 
     * @return int
     */
    public function getStockCountAttribute()
    {
        return $this->stocks()->where('is_sold', false)->count();
    }
    
    /**
     * Get the available stock entries for the product.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function availableStocks()
    {
        return $this->stocks()->where('is_sold', false)->get();
    }
}
