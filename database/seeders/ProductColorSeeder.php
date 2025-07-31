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
            '#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#000000',
            '#FFFFFF', '#808080', '#FFA500', '#800080', '#FFC0CB',
            '#8B0000', '#006400', '#00008B', '#404040', '#4B0082',
            '#FFB6C1', '#90EE90', '#ADD8E6', '#FFFFE0', '#D3D3D3',
            '#C0C0C0', '#FFD700', '#CD7F32', '#B87333', '#E5E4E2',
            '#A52A2A', '#F5F5DC', '#FFFDD0', '#F0E68C', '#808000',
            '#FF8C00', '#FF1493', '#00BFFF', '#32CD32', '#9370DB',
            '#FFB3BA', '#B3FFB3', '#B3D9FF', '#FFFFB3', '#FFB3D9',
            '#39FF14', '#FF6EC7', '#4DEEEA', '#FFFF00', '#FF9E00',
            '#8B4513', '#D2691E', '#F4A460', '#696969', '#DEB887'
        ];
        
        foreach ($colors as $colorCode) {
            ProductColor::create([
                'color_code' => $colorCode
            ]);
        }
        
        $this->command->info('50 product colors created successfully!');
    }
}
