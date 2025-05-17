<?php

namespace App\Policies;

use App\Models\OrderDocument;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderDocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderDocument $orderDocument): bool
    {
        // Admin dapat melihat semua dokumen pesanan
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Pengguna hanya dapat melihat dokumen pesanan miliknya sendiri
        $order = $orderDocument->order;
        return $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin yang dapat membuat dokumen pesanan
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderDocument $orderDocument): bool
    {
        // Hanya admin yang dapat mengubah dokumen pesanan
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderDocument $orderDocument): bool
    {
        // Hanya admin yang dapat menghapus dokumen pesanan
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderDocument $orderDocument): bool
    {
        // Hanya admin yang dapat memulihkan dokumen pesanan yang dihapus
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderDocument $orderDocument): bool
    {
        // Hanya admin yang dapat menghapus permanen dokumen pesanan
        return $user->hasRole('admin');
    }
    
    /**
     * Determine whether the user can download the document.
     */
    public function download(User $user, OrderDocument $orderDocument): bool
    {
        // Admin dapat mengunduh semua dokumen pesanan
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Pengguna hanya dapat mengunduh dokumen pesanan miliknya sendiri
        $order = $orderDocument->order;
        return $user->id === $order->user_id;
    }
} 