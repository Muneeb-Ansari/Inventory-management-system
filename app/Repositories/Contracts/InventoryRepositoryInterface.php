<?php

namespace App\Repositories\Contracts;

interface InventoryRepositoryInterface
{
    public function getInventoryReport($locationId, $startDate, $endDate, $productId = null);

    public function createPurchase(array $data);

    public function createSale(array $data);

    public function updateStockMovement($type, $model);

    public function checkStockAvailability($locationId, $productId, $quantity);

    public function getProductStockHistory($locationId, $productId, $startDate, $endDate);

    public function setOpeningStock($locationId, $productId, $date, $quantity, $rate);
}
