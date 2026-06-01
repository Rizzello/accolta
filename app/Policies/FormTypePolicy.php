<?php

namespace App\Policies;

use App\Models\FormType;
use App\Models\User;

class FormTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, FormType $formType): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, FormType $formType): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, FormType $formType): bool
    {
        return $user->is_admin;
    }

    public function deleteAny(User $user): bool
    {
        return $user->is_admin;
    }
}
