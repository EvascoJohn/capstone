<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;



class DealerhipCalculations
{
    static function calculateSum(...$args): float {
        $sum = 0;
        foreach($args as $arg){$sum += $arg;}
        if($sum < 0){
            $sum = 0;
        }
        return $sum;
    }

    static function calculateTotalInterest(float $monthly_interest_rate, $term): float
    {
        return ($monthly_interest_rate / 100) * $term;
    }

    static function calculateDownPaymentPercentage(float $down_payment_percentage): float
    {
        return $down_payment_percentage / 100;
    }

    static function calculateDownPaymentAmount(float $initial_price, float $down_payment_percentage): float
    {
        return $initial_price * $down_payment_percentage;
    }

    static function calculateAmountToBeFinanced(float $initial_price, $down_payment_amount): float
    {
        return $initial_price - $down_payment_amount;
    }

    static function calculateTotalCost(float $amount_to_be_financed, float $total_interest, float $down_payment_amount): float
    {
        return ($amount_to_be_financed * $total_interest )+ $down_payment_amount;
    }

    static function calculateTotalCostWithoutDP(float $amount_to_be_financed, float $total_interest): float
    {
        return $amount_to_be_financed * $total_interest;
    }

    static function calculateMonthlyPayment(float $total_cost,int $loan_term): float{
        return ($total_cost / $loan_term) ;
    }
}