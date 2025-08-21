<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupportTicketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view_any_ticket');
    }

    /**
     * Determine whether the user can view the support ticket.
     */
    public function view(User $user, SupportTicket $ticket)
    {
        // Example: A user can view their own ticket or has a specific permission
        return $user->id === $ticket->user_id || $user->hasPermissionTo('view_ticket');
    }

    /**
     * Determine whether the user can create support tickets.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create_ticket');
    }

    /**
     * Determine whether the user can update the support ticket.
     */
    public function update(User $user, SupportTicket $ticket)
    {
        // Example: A user can update their own ticket or has a specific permission
        return $user->id === $ticket->user_id || $user->hasPermissionTo('update_ticket');
    }

    /**
     * Determine whether the user can delete the support ticket.
     */
    public function delete(User $user, SupportTicket $ticket)
    {
        // Example: A user can delete their own ticket or has a specific permission
        return $user->id === $ticket->user_id || $user->hasPermissionTo('delete_ticket');
    }
}
