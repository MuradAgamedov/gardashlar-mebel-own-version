<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductColor;
use App\Models\ProductColorTranslate;
use App\Models\Language;

class ProductColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = Language::where('status', true)->get();
        
        $colors = [
            // Basic Colors
            ['az' => 'Qırmızı', 'en' => 'Red', 'hex' => '#FF0000'],
            ['az' => 'Yaşıl', 'en' => 'Green', 'hex' => '#00FF00'],
            ['az' => 'Mavi', 'en' => 'Blue', 'hex' => '#0000FF'],
            ['az' => 'Sarı', 'en' => 'Yellow', 'hex' => '#FFFF00'],
            ['az' => 'Qara', 'en' => 'Black', 'hex' => '#000000'],
            ['az' => 'Ağ', 'en' => 'White', 'hex' => '#FFFFFF'],
            ['az' => 'Boz', 'en' => 'Gray', 'hex' => '#808080'],
            ['az' => 'Narıncı', 'en' => 'Orange', 'hex' => '#FFA500'],
            ['az' => 'Bənövşəyi', 'en' => 'Purple', 'hex' => '#800080'],
            ['az' => 'Çəhrayı', 'en' => 'Pink', 'hex' => '#FFC0CB'],
            
            // Dark Colors
            ['az' => 'Tünd qırmızı', 'en' => 'Dark Red', 'hex' => '#8B0000'],
            ['az' => 'Tünd yaşıl', 'en' => 'Dark Green', 'hex' => '#006400'],
            ['az' => 'Tünd mavi', 'en' => 'Dark Blue', 'hex' => '#00008B'],
            ['az' => 'Tünd boz', 'en' => 'Dark Gray', 'hex' => '#404040'],
            ['az' => 'Tünd bənövşəyi', 'en' => 'Dark Purple', 'hex' => '#4B0082'],
            
            // Light Colors
            ['az' => 'Açıq qırmızı', 'en' => 'Light Red', 'hex' => '#FFB6C1'],
            ['az' => 'Açıq yaşıl', 'en' => 'Light Green', 'hex' => '#90EE90'],
            ['az' => 'Açıq mavi', 'en' => 'Light Blue', 'hex' => '#ADD8E6'],
            ['az' => 'Açıq sarı', 'en' => 'Light Yellow', 'hex' => '#FFFFE0'],
            ['az' => 'Açıq boz', 'en' => 'Light Gray', 'hex' => '#D3D3D3'],
            
            // Metallic Colors
            ['az' => 'Gümüş', 'en' => 'Silver', 'hex' => '#C0C0C0'],
            ['az' => 'Qızıl', 'en' => 'Gold', 'hex' => '#FFD700'],
            ['az' => 'Bürünc', 'en' => 'Bronze', 'hex' => '#CD7F32'],
            ['az' => 'Mis', 'en' => 'Copper', 'hex' => '#B87333'],
            ['az' => 'Platin', 'en' => 'Platinum', 'hex' => '#E5E4E2'],
            
            // Natural Colors
            ['az' => 'Qəhvəyi', 'en' => 'Brown', 'hex' => '#A52A2A'],
            ['az' => 'Bej', 'en' => 'Beige', 'hex' => '#F5F5DC'],
            ['az' => 'Krem', 'en' => 'Cream', 'hex' => '#FFFDD0'],
            ['az' => 'Xaki', 'en' => 'Khaki', 'hex' => '#F0E68C'],
            ['az' => 'Zeytun', 'en' => 'Olive', 'hex' => '#808000'],
            
            // Bright Colors
            ['az' => 'Turuncu', 'en' => 'Bright Orange', 'hex' => '#FF8C00'],
            ['az' => 'Çəhrayı', 'en' => 'Bright Pink', 'hex' => '#FF1493'],
            ['az' => 'Mavi', 'en' => 'Bright Blue', 'hex' => '#00BFFF'],
            ['az' => 'Yaşıl', 'en' => 'Bright Green', 'hex' => '#32CD32'],
            ['az' => 'Bənövşəyi', 'en' => 'Bright Purple', 'hex' => '#9370DB'],
            
            // Pastel Colors
            ['az' => 'Pastel qırmızı', 'en' => 'Pastel Red', 'hex' => '#FFB3BA'],
            ['az' => 'Pastel yaşıl', 'en' => 'Pastel Green', 'hex' => '#B3FFB3'],
            ['az' => 'Pastel mavi', 'en' => 'Pastel Blue', 'hex' => '#B3D9FF'],
            ['az' => 'Pastel sarı', 'en' => 'Pastel Yellow', 'hex' => '#FFFFB3'],
            ['az' => 'Pastel çəhrayı', 'en' => 'Pastel Pink', 'hex' => '#FFB3D9'],
            
            // Neon Colors
            ['az' => 'Neon yaşıl', 'en' => 'Neon Green', 'hex' => '#39FF14'],
            ['az' => 'Neon çəhrayı', 'en' => 'Neon Pink', 'hex' => '#FF6EC7'],
            ['az' => 'Neon mavi', 'en' => 'Neon Blue', 'hex' => '#4DEEEA'],
            ['az' => 'Neon sarı', 'en' => 'Neon Yellow', 'hex' => '#FFFF00'],
            ['az' => 'Neon narıncı', 'en' => 'Neon Orange', 'hex' => '#FF9E00'],
            
            // Earth Tones
            ['az' => 'Təbii', 'en' => 'Natural', 'hex' => '#8B4513'],
            ['az' => 'Torpaq', 'en' => 'Earth', 'hex' => '#D2691E'],
            ['az' => 'Qum', 'en' => 'Sand', 'hex' => '#F4A460'],
            ['az' => 'Daş', 'en' => 'Stone', 'hex' => '#696969'],
            ['az' => 'Ağac', 'en' => 'Wood', 'hex' => '#DEB887']
        ];
        
        foreach ($colors as $index => $color) {
            $productColor = ProductColor::create([
                'order' => $index + 1,
                'status' => true
            ]);
            
            // Create translations for each language
            foreach ($languages as $language) {
                $langCode = $language->lang_code;
                $title = $color[$langCode] ?? $color['en']; // Fallback to English
                
                ProductColorTranslate::create([
                    'product_color_id' => $productColor->id,
                    'language_id' => $language->id,
                    'title' => $title
                ]);
            }
        }
        
        $this->command->info('50 product colors created successfully!');
    }
}
