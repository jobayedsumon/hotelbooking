<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        $faker = Faker\Factory::create();

        for ($i=0; $i<10; $i++) {
            \App\Room::create([
               'room_number' => $faker->unique()->randomNumber(3),
               'price' => $faker->randomFloat(null, 1000.0, 5000.0),
                'locked' => $faker->boolean,
                'max_persons' => $faker->randomDigit,
                'room_type' => $faker->boolean ? 'single' : 'double'
            ]);
        }
    }
}
