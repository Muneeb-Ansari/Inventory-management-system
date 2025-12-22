<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'product_id',
        'movement_date',
        'movement_type',
        'movementable_type',
        'movementable_id',
        'quantity_in',
        'quantity_out',
        'rate',
        'balance',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'quantity_in' => 'decimal:2',
        'quantity_out' => 'decimal:2',
        'rate' => 'decimal:2',
        'balance' => 'decimal:2',
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

    public function movementable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    public function scopeByLocation(Builder $query, $locationId): Builder
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeByProduct(Builder $query, $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByType(Builder $query, $type): Builder
    {
        return $query->where('movement_type', $type);
    }
}
