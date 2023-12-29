<?php

namespace App\Policies;

use App\Events\PaymentMade;
use App\Models\Payment;
use App\Models\Payments;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Auth\Access\Response;

class PaymentsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        if($user::class == Customer::class || $user->hasAnyPermission("read: payment")){
            return true;
        };
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Payment $payments): bool
    {
        if($user::class == Customer::class || $user->hasAnyPermission("read: payment")){
            return true;
        };
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        
        if($user::class == Customer::class || $user->hasAnyPermission("read: payment")){
            return true;
        };
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Payment $payments): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Payment $payments): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Payment $payments): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Payment $payments): bool
    {
        return false;
    }
}
