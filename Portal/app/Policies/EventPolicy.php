<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class EventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Event $event): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): Response
    {
        return $event->userStatus($user->id) === 2
            ? Response::allow()
            : Response::deny('You are not the creator of this event');
    }

    public function kick(User $user, Event $event): Response
    {
        return $event->userStatus($user->id) === 2
            ? Response::allow()
            : Response::deny('You are not allowed to kick others from this event');
    }

    public function invite(User $user, Event $event): Response
    {
        return $event->userStatus($user->id) > 0
            ? Response::allow()
            : Response::deny('You are not allowed to invite others to this event');
    }

    public function viewJoinCredentials(User $user, Event $event): Response
    {
        return $event->userStatus($user->id) > 0
            ? Response::allow()
            : Response::deny('You are not allowed to view IP and Password assigned to this event');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Event $event): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        //
    }
}
