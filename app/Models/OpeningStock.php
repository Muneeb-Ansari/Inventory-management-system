<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'product_id',
        'date',
        'quantity',
        'rate',
        'amount',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'movementable');
    }

    // Auto-calculate amount
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($stock) {
            $stock->amount = $stock->quantity * $stock->rate;
        });
    }
}
