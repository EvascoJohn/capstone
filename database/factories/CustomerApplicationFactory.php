<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerApplication>
 */
class CustomerApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_status' => 'pending',
            'application_is_new' => true,
            'is_application_approved' => false,
            'is_application_rejected' => false, //password

            // 'unit_model' => "Mio i 125",
            'unit_term' => 36,
            'unit_monthly_amort' => 2191.66,
            'unit_ttl_dp' => 12,
            'unit_srp' => 78000,
            'unit_type' => $this->faker->randomElement(['old', 'new']),
            'unit_amort_fin' => 2191.66,
            'unit_mode_of_payment' => "office",

            'applicant_surname' => $this->faker->lastName(),
            'applicant_middlename' => $this->faker->firstName(),
            'applicant_lastname' => $this->faker->firstName(),
            'applicant_birthday' => $this->faker->date(),
            'applicant_civil_status' => $this->faker->randomElement(['single', 'married', 'separated', 'widow']),
            'applicant_present_address' => $this->faker->address(),
            'applicant_previous_address' => $this->faker->address(),
            'applicant_provincial_address' => $this->faker->address(),
            'applicant_lived_there' => $this->faker->randomElement(['Yes', 'No']),
            'applicant_house' => $this->faker->randomElement(['w/ parents', 'Rental', 'Owned']),
            'applicant_valid_id' => $this->faker->randomElement(['Drivers Liscense', 'National ID']),
            'applicant_telephone' => $this->faker->phoneNumber(),

            'applicants_basic_monthly_salary' => $this->faker->randomFloat(2, 1000, 10000),
            'applicants_allowance_commission' => $this->faker->randomFloat(2, 0, 1000),
            'applicants_deductions' => $this->faker->randomFloat(2, 0, 500),
            'applicants_net_monthly_income' => $this->faker->randomFloat(2, 2000, 8000),


            'spouses_basic_monthly_salary' => $this->faker->randomFloat(2, 1000, 10000),
            'spouse_allowance_commision' => $this->faker->randomFloat(2, 0, 1000),
            'spouse_deductions' => $this->faker->randomFloat(2, 0, 500),
            'spouse_net_monthly_income' => $this->faker->randomFloat(2, 2000, 8000),
            'properties' => null,
            'other_income' => $this->faker->randomFloat(2, 0, 1000),
            'total_expenses' => $this->faker->randomFloat(2, 500, 2000),
            'gross_monthly_income' => $this->faker->randomFloat(2, 3000, 10000),
            'net_monthly_income' => $this->faker->randomFloat(2, 2500, 8000),


        ];
    }
}
