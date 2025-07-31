<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductColor;

class ProductColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            // Red variations
            '#FF0000', '#DC143C', '#B22222', '#8B0000', '#FF4500',
            
            // Green variations
            '#00FF00', '#32CD32', '#228B22', '#006400', '#90EE90',
            
            // Blue variations
            '#0000FF', '#4169E1', '#00008B', '#191970', '#00BFFF',
            
            // Yellow variations
            '#FFFF00', '#FFD700', '#DAA520', '#B8860B', '#F0E68C',
            
            // Purple variations
            '#800080', '#9370DB', '#4B0082', '#8B008B', '#9932CC',
            
            // Orange variations
            '#FFA500', '#FF8C00', '#FF6347', '#FF7F50', '#FF69B4',
            
            // Pink variations
            '#FFC0CB', '#FF1493', '#FFB6C1', '#FFC0CB', '#FF69B4',
            
            // Brown variations
            '#A52A2A', '#8B4513', '#D2691E', '#CD853F', '#DEB887',
            
            // Gray variations
            '#808080', '#696969', '#2F4F4F', '#708090', '#C0C0C0',
            
            // White variations
            '#FFFFFF', '#F5F5F5', '#F0F8FF', '#F0FFF0', '#FFFACD'
        ];
        
        foreach ($colors as $colorCode) {
            ProductColor::create([
                'color_code' => $colorCode
            ]);
        }
        
        $this->command->info('50 product colors created successfully!');
    }
}
