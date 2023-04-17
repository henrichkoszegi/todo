<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TodoPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Todo $todo): bool
    {
        return $this->update($user, $todo);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Todo $todo): bool
    {
        return $this->update($user, $todo);
    }

    /**
     * Determine whether the user can mark model as completed.
     */
    public function markCompleted(User $user, Todo $todo): bool
    {
        return ! $todo->is_completed &&
            ($this->update($user, $todo) || $todo->whereRelation('shares', 'user_id', '=', $user->id)->exists());
    }
}
