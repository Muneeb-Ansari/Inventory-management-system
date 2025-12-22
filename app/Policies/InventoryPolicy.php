<?php

namespace App\Policies;

use App\Models\User, Inventory;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    // Admin can do everything
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager', 'shopman']);
    }

    public function view(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager', 'shopman']);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function update(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function delete(User $user)
    {
        return $user->hasRole('admin');
    }

    public function createPurchase(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function createLocation(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function createSale(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager', 'shopman']);
    }

    public function viewReport(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function downloadReport(User $user)
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function setOpeningStock(User $user)
    {
        return $user->hasRole('admin');
    }
}
