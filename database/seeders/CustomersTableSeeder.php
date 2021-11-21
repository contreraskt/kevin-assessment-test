<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\PurchaseTransaction;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Customer::Truncate();
        // PurchaseTransaction::Truncate();
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $gender = $faker->randomElement(['male','female'])[0];
            $customer =  Customer::create([
                'first_name' => $faker->firstName($gender),
                'last_name' => $faker->lastName,
                'gender' => $gender,
                'date_of_birth' => $faker->date($format = 'Y-m-d', $max = 'now'),
                'contact_number' => $faker->phoneNumber,
                'email' => $faker->safeEmail
            ]);

            for ($y = 0; $y<=5; $y++){
                PurchaseTransaction::create([
                    'customer_id' => $customer->id,
                    'total_spent' => $faker->randomFloat(2, 10, 200),
                    'total_saving' => $faker->randomFloat(2, 10, 50),
                    'transaction_at' => $faker->dateTimeBetween($startDate = '-3 months', $endDate = 'now')
                ]);
            }
        }
    }
}
