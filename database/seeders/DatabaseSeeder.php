<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Post;
use App\Models\User;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\ShopService;
use App\Models\ShopCategory;
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

        //users array
        $users = [
            [
                'name' => 'BeBee',
                'email' => 'bebee@gmail.com',
                'password' => Hash::make('bebee7121'),
                'type' => 'admin'
            ],
            [
                'name' => 'Thit Lwin',
                'email' => 'thitlwin@gmail.com',
                'password' => Hash::make('12345678'),
                'type' => 'admin'
            ]
        ];

        //users seeding
        foreach($users as $user){
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'type' => $user['type']
            ]);
        }

        //shop categories array
        $shop_categories = ['Accessories','Spare parts','Showroom'];

        //shop categories seeding
        foreach($shop_categories as $shop_category){
            ShopCategory::create([
                'name' => $shop_category
            ]);
        }

        //shop services array
        $shop_services = ['Services','Accessories'];

        //shop services seeding
        foreach($shop_services as $shop_service){
            ShopService::create([
                'name' => $shop_service
            ]);
        }

        //car brands array
        $brands = ['Honda','Suzuki','Toyota'];

        //car brands seeding
        foreach($brands as $brand){
            CarBrand::create([
                'name' => $brand
            ]);
        }

        //car models array
        $models = [
            [
                'brand_id' => 1,
                'name' => 'Fit',
                'image' => 'nissan-gtr-image-01.jpg'
            ],
            [
                'brand_id' => 1,
                'name' => 'Insight',
                'image' => 'nissan-gtr-image-01.jpg'
            ],
            [
                'brand_id' => 1,
                'name' => 'Civic',
                'image' => 'nissan-gtr-image-01.jpg'
            ],
            [
                'brand_id' => 2,
                'name' => 'Swift',
                'image' => 'nissan-gtr-image-01.jpg'
            ],
            [
                'brand_id' => 2,
                'name' => 'Ciaz',
                'image' => 'nissan-gtr-image-01.jpg'
            ],
            [
                'brand_id' => 2,
                'name' => 'Ertiga',
                'image' => 'nissan-gtr-image-01.jpg'
            ],
            [
                'brand_id' => 3,
                'name' => 'Wish',
                'image' => 'nissan-gtr-image-01.jpg'
            ],
            [
                'brand_id' => 3,
                'name' => 'Crown',
                'image' => 'nissan-gtr-image-01.jpg'
            ],
            [
                'brand_id' => 3,
                'name' => 'Hilux',
                'image' => 'nissan-gtr-image-01.jpg'
            ]
        ];

        //car models seeding
        foreach($models as $model){
            CarModel::create([
                'brand_id' => $model['brand_id'],
                'name' => $model['name'],
                'image' => $model['image']
            ]);

        }

    }
}
