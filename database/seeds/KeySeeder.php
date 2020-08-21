<?php

use App\Key;
use Illuminate\Database\Seeder;

class KeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Key::class, 100)->create();
    }
}
