<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        foreach (range(1,200000) as $i){
            User::create([
                'birth_date'=>date('Y-m-d'),
                'last_activity'=>date('Y-m-d'),
                'last_login'=>date('Y-m-d'),
                'username'=>$faker->firstname,
                'first_name'=>$faker->firstname,
                'middle_name'=>$faker->firstname,
                'last_name'=>$faker->firstname,
                'user_mobile'=>'0',
                'gender'=>'female',
                'level'=>0,
                'is_client'=>1,
                'is_confirmed'=>1,
                'user_data'=>'{"home_branch":"1","premiere_status","0"}',
                'device_data'=>'{}',
                'user_picture'=>'Registration.png',
                'is_active'=>1,
                'user_address'=>'Manila',
                'email'=> $faker->unique()->safeEmail,
                'password'=>bcrypt('12345')
            ]);
        }
    }
}
