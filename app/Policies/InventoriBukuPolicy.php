<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InventoriBuku;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoriBukuPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_inventori::buku');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InventoriBuku $inventoriBuku): bool
    {
        return $user->can('view_inventori::buku');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_inventori::buku');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InventoriBuku $inventoriBuku): bool
    {
        return $user->can('update_inventori::buku');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InventoriBuku $inventoriBuku): bool
    {
        return $user->can('delete_inventori::buku');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_inventori::buku');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, InventoriBuku $inventoriBuku): bool
    {
        return $user->can('force_delete_inventori::buku');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_inventori::buku');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, InventoriBuku $inventoriBuku): bool
    {
        return $user->can('restore_inventori::buku');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_inventori::buku');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, InventoriBuku $inventoriBuku): bool
    {
        return $user->can('replicate_inventori::buku');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_inventori::buku');
    }
}
