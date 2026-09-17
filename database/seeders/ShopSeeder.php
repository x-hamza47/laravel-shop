<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('data/shop.json')), true);
       
        DB::table('categories')->insert($data['categories']);
        DB::table('sub_categories')->insert($data['sub_categories']);
        DB::table('brands')->insert($data['brands']);
        
        foreach ($data['products'] as $product) {
            $images = $product['images'];
            unset($product['images']);

            DB::table('products')->insert($product);

            foreach ($images as $index => $image) {
                DB::table('product_images')->insert([
                    'product_id' => $product['id'],
                    'image' => $image,
                    'sort_order' => $index,
                ]);
            }
        }

    }
}
