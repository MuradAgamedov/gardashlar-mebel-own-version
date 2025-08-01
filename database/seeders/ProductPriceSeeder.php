<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products
        $products = Product::all();
        
        foreach ($products as $product) {
            // Generate random price between 1000 and 5000
            $randomPrice = rand(1000, 5000);
            
            // Update the product with the random price
            $product->update([
                'price' => $randomPrice
            ]);
        }
        
        $this->command->info("Updated " . $products->count() . " products with random prices between 1000-5000.");
    }
} 