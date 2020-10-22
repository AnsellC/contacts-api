<?php

namespace App\Policies;

use App\Models\ContactEntry;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactEntryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ContactEntry  $contactEntry
     * @return mixed
     */
    public function view(User $user, ContactEntry $contactEntry)
    {
        return $user->id === $contactEntry->contact->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ContactEntry  $contactEntry
     * @return mixed
     */
    public function update(User $user, ContactEntry $contactEntry)
    {
        return $user->id === $contactEntry->contact->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ContactEntry  $contactEntry
     * @return mixed
     */
    public function delete(User $user, ContactEntry $contactEntry)
    {
        return $user->id === $contactEntry->contact->user_id;
    }

}
