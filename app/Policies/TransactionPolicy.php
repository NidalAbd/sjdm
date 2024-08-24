<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any transactions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        // Only allow admins or users with the 'view_any_transaction' permission
        return $user->hasRole('admin') || $user->hasPermissionTo('view_any_transaction');
    }

    /**
     * Determine whether the user can view a specific transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return mixed
     */
    public function view(User $user, Transaction $transaction)
    {
        // Allow viewing if the user owns the transaction or is an admin
        return $user->id === $transaction->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create transactions.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        // Allow creating transactions if the user has the 'create_transaction' permission
        return $user->hasPermissionTo('create_transaction');
    }

    /**
     * Determine whether the user can update a transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return mixed
     */
    public function update(User $user, Transaction $transaction)
    {
        // Allow updating if the user owns the transaction or is an admin
        return $user->id === $transaction->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete a transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return mixed
     */
    public function delete(User $user, Transaction $transaction)
    {
        // Allow deleting if the user is an admin
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manually add balance for another user.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function add_balance(User $user)
    {
        // Only allow admins to manually add balance for other users
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore a transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return mixed
     */
    public function restore(User $user, Transaction $transaction)
    {
        // Allow restoring if the user is an admin
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete a transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return mixed
     */
    public function forceDelete(User $user, Transaction $transaction)
    {
        // Allow force deleting if the user is an admin
        return $user->hasRole('admin');
    }
}
