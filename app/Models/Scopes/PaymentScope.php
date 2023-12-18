<?php

namespace App\Models\Scopes;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PaymentScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if(auth()->check()){
            if(auth()->user()::class == Customer::class){
                //checks customer_id.
                $builder->where('author_id', auth()->user()->id);
            }
            if(auth()->user()::class == User::class){
                //checks branch_id and online application
                $builder->where('branch_id', auth()->user()->branch_id);
            }
        };
    }
}
