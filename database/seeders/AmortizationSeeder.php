<?php

namespace Database\Seeders;

use App\Filament\Pages\EditCustomerApplicationMaintenance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmortizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ["term" => "6", "amortization" => "0.19375675675675677"],
            ["term" => "12", "amortization" => "0.11208108108108109"],
            ["term" => "18", "amortization" => "0.08486486486486486"],
            ["term" => "24", "amortization" => "0.07124324324324324"],
            ["term" => "30", "amortization" => "0.06308108108108108"],
            ["term" => "36", "amortization" => "0.05762162162162162"],
        ];

        // Convert the array to a JSON string
        $json_data = json_encode($data);

        // Insert data into the database
        DB::table('customer_application_maintenances')->insert(['monthly_amortizations' => $json_data]);
    }
}
