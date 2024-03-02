<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\ShopCategory;
use App\Models\ShopService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'name' => 'BeBee',
            'email' => 'bebee@gmail.com',
            'password' => Hash::make('bebee7121'),
            'type' => 'admin'
        ]);

        \App\Models\User::create([
            'name' => 'Thit Lwin',
            'email' => 'thitlwin@gmail.com',
            'password' => Hash::make('128'),
            'type' => 'admin'
        ]);

        ShopCategory::create([
            'name' => 'Accessories'
        ]);

        ShopCategory::create([
            'name' => 'Spare parts'
        ]);

        ShopCategory::create([
            'name' => 'Showroom'
        ]);

        ShopService::create([
            'name' => 'Services'
        ]);

        ShopService::create([
            'name' => 'Accessories'
        ]);
    }
}
