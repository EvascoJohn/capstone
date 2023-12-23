<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\CustomerApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        if($user::class == Customer::class || $user->hasAnyPermission("read: customer-application")){
            return true;
        };
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, CustomerApplication $customerApplication): bool
    {
        if($user::class == Customer::class || $user->hasAnyPermission("read: customer-application")){
            return true;
        };
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        if($user::class == Customer::class || $user->hasAnyPermission("read: customer-application")){
            return true;
        };
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, CustomerApplication $customerApplication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, CustomerApplication $customerApplication): bool
    {
        return $user->hasAnyPermission(
            "delete: customer-application"
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CustomerApplication $customerApplication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CustomerApplication $customerApplication): bool
    {
        return false;
    }
}
