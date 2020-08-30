<?php

use App\Distributor;
use App\Platform;
use Illuminate\Database\Seeder;

class DistributorsSeeder extends Seeder
{
    protected $distributors = [
        'PlayStation 4' => [
            'PlayStation Store'
        ],
        'Xbox One' => [
            'Microsoft Store'
        ],
        'PC' => [
            'Steam', 'GOG', 'Uplay', 'Epic Games Store'
        ],
        'Nintendo Switch' => [
            'Nintendo eShop'
        ]
    ];

    public function run()
    {
        foreach ($this->distributors as $platform => $distributors) {
            $platform = Platform::create(['name' => $platform]);

            foreach ($distributors as $distributor) {
                Distributor::create(['name' => $distributor, 'platform_id' => $platform->id]);
            }
        }
    }
}
