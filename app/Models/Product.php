<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'unit',
        'minimum_stock',
        'is_active',
        'is_discontinued',
        'discontinued_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_discontinued' => 'boolean',
        'discontinued_at' => 'datetime',
        'minimum_stock' => 'decimal:2',
    ];

    public function openingStocks()
    {
        return $this->hasMany(OpeningStock::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeNotDiscontinued(Builder $query): Builder
    {
        return $query->where('is_discontinued', false);
    }

    public function scopeDiscontinued(Builder $query): Builder
    {
        return $query->where('is_discontinued', true);
    }

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }

    public function scopeLowStock(Builder $query, $locationId): Builder
    {
        return $query->whereHas('stockMovements', function($q) use ($locationId) {
            $q->where('location_id', $locationId)
              ->whereRaw('balance < products.minimum_stock');
        });
    }

    // Methods
    public function discontinue()
    {
        $this->update([
            'is_discontinued' => true,
            'discontinued_at' => now(),
            'is_active' => false,
        ]);
    }

    public function getCurrentStock($locationId)
    {
        return $this->stockMovements()
            ->where('location_id', $locationId)
            ->latest('movement_date')
            ->latest('id')
            ->value('balance') ?? 0;
    }
}
