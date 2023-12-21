<?php

namespace App\Models;


class StatementOfMonthlyIncomeHelper
{
    static function calculateNetIncome(?float $additionals, ?float $deductions):float
    {
        $net = $additionals - $deductions;
        if($net < 0){
            return 0;
        }
        return $net;
    }
}