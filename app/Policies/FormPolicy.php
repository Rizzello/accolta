<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;

class FormPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Form $form): bool
    {
        return $form->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, Form $form): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, Form $form): bool
    {
        return $user->is_admin;
    }

    public function deleteAny(User $user): bool
    {
        return $user->is_admin;
    }
}
