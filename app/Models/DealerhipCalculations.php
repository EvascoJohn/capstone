<?php

namespace App\Models;

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
        return ($amount_to_be_financed * $total_interest ) + $down_payment_amount;
    }

    static function calculateTotalCostWithoutDP(float $monthly_payment, float $term): float
    {
        return $monthly_payment * $term;
    }

    static function calculateMonthlyPayment(float $total_cost,int $loan_term): float{
        return ($total_cost * $loan_term);
    }

    static function formatMonthlyAmortizationsJson($jsonString)
    {
        // Decode the JSON string into an array
        $inputArray = json_decode($jsonString, true);
    
        // Check if decoding was successful and data is an array
        if (is_array($inputArray)) {
            $outputArray = [];
    
            foreach ($inputArray as $item) {
                $outputArray['monthly_amortizations'][] = [
                    'term' => $item['term'],
                    'amortization' => $item['amortization'],
                ];
            }
    
            return $outputArray;
        } else {
            // Handle the case where decoding fails
            return ['error' => 'Invalid JSON format'];
        }
    }

    static function getAmortizationByTerm($jsonContent, $searchTerm)
    {
        // Decode JSON to an array of objects
        $data = json_decode($jsonContent);
    
        // Check if decoding was successful and data is an array
        if (is_array($data)) {
            // Loop through each object in the array
            foreach ($data as $item) {
                // Check if "term" and "amortization" properties exist before using them
                if (isset($item->term) && isset($item->amortization)) {
                    // If the current item's "term" matches the search term, return the corresponding "amortization"
                    if ($item->term == $searchTerm) {
                        return $item->amortization;
                    }
                }
            }
        } else {
            echo "Invalid JSON format.\n";
        }
    
        // Return null if the search term is not found
        return null;
    }

    
    static function extractKeyValuePairs($jsonContent):array
    {
        // Decode JSON to an array of objects
        $data = json_decode($jsonContent);

        // Initialize an empty associative array
        $outputArray = [];

        // Check if decoding was successful and data is an array
        if (is_array($data)) {
            // Loop through each object in the array
            foreach ($data as $item) {
                // Check if "term" property exists before using it
                if (isset($item->term)) {
                    // Add a key-value pair to the output array
                    $outputArray[$item->term] = $item->term;
                }
            }
        }
    
        // Output the resulting associative array
        return $outputArray;
    }

}