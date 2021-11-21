<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;

class VouchersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
         Voucher::Truncate();
        $faker = \Faker\Factory::create();

        for($v = 0; $v<=1000; $v++) {
            $voucher =  Voucher::create([
                'voucher_code' => $faker->bothify('****-****-****-****')
            ]);
        }
    }
}
