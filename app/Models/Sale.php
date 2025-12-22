<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_no',
        'location_id',
        'user_id',
        'sale_date',
        'customer_name',
        'remarks',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'movementable');
    }

    // Scopes
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeByLocation(Builder $query, $locationId): Builder
    {
        return $query->where('location_id', $locationId);
    }

    // Methods
    public function calculateTotal()
    {
        $this->total_amount = $this->items()->sum('amount');
        $this->save();
    }
}
