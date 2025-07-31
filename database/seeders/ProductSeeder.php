<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductTranslate;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Toplantı masaları (16)
            [
                'category_id' => 16,
                'az' => [
                    'title' => 'Oval Toplantı Masası – Taxta Nümunə',
                    'description' => 'Oval forması və taxta teksturası ilə müasir ofis mühitinə uyğun toplantı masası.',
                    'seo_title' => 'Oval Toplantı Masası',
                    'seo_keywords' => 'toplantı masası, oval masa, taxta masa',
                    'seo_description' => 'Müasir dizayna sahib oval toplantı masası.',
                    'material' => 'MDF, şpon',
                    'size' => '240x120 sm',
                ],
                'en' => [
                    'title' => 'Oval Conference Table – Wooden Style',
                    'description' => 'Modern oval conference table with a wooden finish suitable for executive offices.',
                    'seo_title' => 'Oval Conference Table',
                    'seo_keywords' => 'conference table, oval table, wooden design',
                    'seo_description' => 'Oval table with modern and wooden look for meetings.',
                    'material' => 'MDF, veneer',
                    'size' => '240x120 cm',
                ],
            ],
            [
                'category_id' => 16,
                'az' => [
                    'title' => 'Dikdörtgen Klassik Toplantı Masası',
                    'description' => 'Klassik dizaynlı geniş toplantı masası 8-10 nəfərlikdir.',
                    'seo_title' => 'Klassik Toplantı Masası',
                    'seo_keywords' => 'klassik masa, toplantı, iclas masası',
                    'seo_description' => 'Klassik görünüşlü iclas otaqları üçün ideal masa.',
                    'material' => 'laminat, metal ayaq',
                    'size' => '300x120 sm',
                ],
                'en' => [
                    'title' => 'Classic Rectangular Meeting Table',
                    'description' => 'A classic rectangular table suitable for 8-10 people, ideal for boardrooms.',
                    'seo_title' => 'Classic Meeting Table',
                    'seo_keywords' => 'classic table, meeting furniture, boardroom',
                    'seo_description' => 'Rectangular executive table for professional meetings.',
                    'material' => 'laminate, metal legs',
                    'size' => '300x120 cm',
                ],
            ],
            // Rəhbər dəstləri (13)
            [
                'category_id' => 13,
                'az' => [
                    'title' => 'Rəhbər Masası və Kreslo Dəsti',
                    'description' => 'Lüks rəhbər masası və ergonomik kreslo dəsti.',
                    'seo_title' => 'Rəhbər Masası Dəsti',
                    'seo_keywords' => 'rəhbər masası, kreslo, ofis dəsti',
                    'seo_description' => 'Peşəkar rəhbərlər üçün lüks masa və kreslo dəsti.',
                    'material' => 'taxta, dəri',
                    'size' => '180x80 sm',
                ],
                'en' => [
                    'title' => 'Executive Desk and Chair Set',
                    'description' => 'Luxury executive desk with ergonomic chair set.',
                    'seo_title' => 'Executive Desk Set',
                    'seo_keywords' => 'executive desk, chair, office set',
                    'seo_description' => 'Luxury desk and chair set for professional executives.',
                    'material' => 'wood, leather',
                    'size' => '180x80 cm',
                ],
            ],
            // VIP-ofis dəstləri (12)
            [
                'category_id' => 12,
                'az' => [
                    'title' => 'VIP Ofis Masa Dəsti',
                    'description' => 'VIP ofislər üçün xüsusi dizayn edilmiş masa dəsti.',
                    'seo_title' => 'VIP Ofis Dəsti',
                    'seo_keywords' => 'VIP ofis, lüks masa, premium',
                    'seo_description' => 'VIP ofislər üçün premium keyfiyyətli masa dəsti.',
                    'material' => 'qaraşma, metal',
                    'size' => '200x90 sm',
                ],
                'en' => [
                    'title' => 'VIP Office Desk Set',
                    'description' => 'Specially designed desk set for VIP offices.',
                    'seo_title' => 'VIP Office Set',
                    'seo_keywords' => 'VIP office, luxury desk, premium',
                    'seo_description' => 'Premium quality desk set for VIP offices.',
                    'material' => 'walnut, metal',
                    'size' => '200x90 cm',
                ],
            ],
            // Menecer dəstləri (14)
            [
                'category_id' => 14,
                'az' => [
                    'title' => 'Menecer Masası və Kreslo',
                    'description' => 'Menecerlər üçün funksional masa və kreslo dəsti.',
                    'seo_title' => 'Menecer Masası',
                    'seo_keywords' => 'menecer masası, ofis kreslosu',
                    'seo_description' => 'Funksional menecer masası və kreslo dəsti.',
                    'material' => 'MDF, mesh',
                    'size' => '160x75 sm',
                ],
                'en' => [
                    'title' => 'Manager Desk and Chair',
                    'description' => 'Functional desk and chair set for managers.',
                    'seo_title' => 'Manager Desk',
                    'seo_keywords' => 'manager desk, office chair',
                    'seo_description' => 'Functional manager desk and chair set.',
                    'material' => 'MDF, mesh',
                    'size' => '160x75 cm',
                ],
            ],
            // İşçi masaları (15)
            [
                'category_id' => 15,
                'az' => [
                    'title' => 'İşçi Masası və Kreslo',
                    'description' => 'İşçilər üçün rahat masa və kreslo dəsti.',
                    'seo_title' => 'İşçi Masası',
                    'seo_keywords' => 'işçi masası, ofis mebeli',
                    'seo_description' => 'İşçilər üçün rahat və funksional masa dəsti.',
                    'material' => 'laminat, plastik',
                    'size' => '140x70 sm',
                ],
                'en' => [
                    'title' => 'Employee Desk and Chair',
                    'description' => 'Comfortable desk and chair set for employees.',
                    'seo_title' => 'Employee Desk',
                    'seo_keywords' => 'employee desk, office furniture',
                    'seo_description' => 'Comfortable and functional desk set for employees.',
                    'material' => 'laminate, plastic',
                    'size' => '140x70 cm',
                ],
            ],
            // Qeydiyyat masaları (17)
            [
                'category_id' => 17,
                'az' => [
                    'title' => 'Qeydiyyat Masası və Kreslo',
                    'description' => 'Qeydiyyat büroları üçün xüsusi masa dəsti.',
                    'seo_title' => 'Qeydiyyat Masası',
                    'seo_keywords' => 'qeydiyyat masası, resepsiya',
                    'seo_description' => 'Qeydiyyat büroları üçün funksional masa dəsti.',
                    'material' => 'MDF, dəri',
                    'size' => '120x60 sm',
                ],
                'en' => [
                    'title' => 'Reception Desk and Chair',
                    'description' => 'Special desk set for reception areas.',
                    'seo_title' => 'Reception Desk',
                    'seo_keywords' => 'reception desk, front desk',
                    'seo_description' => 'Functional desk set for reception areas.',
                    'material' => 'MDF, leather',
                    'size' => '120x60 cm',
                ],
            ],
            // Yazı masaları (18)
            [
                'category_id' => 18,
                'az' => [
                    'title' => 'Yazı Masası və Kreslo',
                    'description' => 'Yazı işləri üçün xüsusi dizayn edilmiş masa.',
                    'seo_title' => 'Yazı Masası',
                    'seo_keywords' => 'yazı masası, yazıçı masası',
                    'seo_description' => 'Yazı işləri üçün rahat və funksional masa.',
                    'material' => 'taxta, metal',
                    'size' => '100x60 sm',
                ],
                'en' => [
                    'title' => 'Writing Desk and Chair',
                    'description' => 'Specially designed desk for writing tasks.',
                    'seo_title' => 'Writing Desk',
                    'seo_keywords' => 'writing desk, writer desk',
                    'seo_description' => 'Comfortable and functional desk for writing tasks.',
                    'material' => 'wood, metal',
                    'size' => '100x60 cm',
                ],
            ],
            // Ofis kresloları (19)
            [
                'category_id' => 19,
                'az' => [
                    'title' => 'Ergonomik Ofis Kreslosu',
                    'description' => 'Ergonomik dizaynlı rahat ofis kreslosu.',
                    'seo_title' => 'Ofis Kreslosu',
                    'seo_keywords' => 'ofis kreslosu, ergonomik',
                    'seo_description' => 'Ergonomik dizaynlı rahat ofis kreslosu.',
                    'material' => 'mesh, metal',
                    'size' => 'Standart',
                ],
                'en' => [
                    'title' => 'Ergonomic Office Chair',
                    'description' => 'Comfortable office chair with ergonomic design.',
                    'seo_title' => 'Office Chair',
                    'seo_keywords' => 'office chair, ergonomic',
                    'seo_description' => 'Comfortable office chair with ergonomic design.',
                    'material' => 'mesh, metal',
                    'size' => 'Standard',
                ],
            ],
            // Oyun masaları (20)
            [
                'category_id' => 20,
                'az' => [
                    'title' => 'Oyun Masası və Kreslolar',
                    'description' => 'Oyun otaqları üçün xüsusi masa dəsti.',
                    'seo_title' => 'Oyun Masası',
                    'seo_keywords' => 'oyun masası, gaming',
                    'seo_description' => 'Oyun otaqları üçün xüsusi dizayn edilmiş masa.',
                    'material' => 'MDF, metal',
                    'size' => '150x75 sm',
                ],
                'en' => [
                    'title' => 'Gaming Desk and Chairs',
                    'description' => 'Special desk set for gaming rooms.',
                    'seo_title' => 'Gaming Desk',
                    'seo_keywords' => 'gaming desk, gaming',
                    'seo_description' => 'Specially designed desk for gaming rooms.',
                    'material' => 'MDF, metal',
                    'size' => '150x75 cm',
                ],
            ],
            // Smart mebellər (21)
            [
                'category_id' => 21,
                'az' => [
                    'title' => 'Smart Masa və Kreslo',
                    'description' => 'Smart funksiyalı müasir masa dəsti.',
                    'seo_title' => 'Smart Masa',
                    'seo_keywords' => 'smart masa, müasir',
                    'seo_description' => 'Smart funksiyalı müasir masa dəsti.',
                    'material' => 'qaraşma, metal',
                    'size' => '180x80 sm',
                ],
                'en' => [
                    'title' => 'Smart Desk and Chair',
                    'description' => 'Modern desk set with smart functions.',
                    'seo_title' => 'Smart Desk',
                    'seo_keywords' => 'smart desk, modern',
                    'seo_description' => 'Modern desk set with smart functions.',
                    'material' => 'walnut, metal',
                    'size' => '180x80 cm',
                ],
            ],
            // Yataq mebeli (22)
            [
                'category_id' => 22,
                'az' => [
                    'title' => 'Yataq Masası və Kreslo',
                    'description' => 'Yataq otaqları üçün rahat masa dəsti.',
                    'seo_title' => 'Yataq Masası',
                    'seo_keywords' => 'yataq masası, yataq otağı',
                    'seo_description' => 'Yataq otaqları üçün rahat və funksional masa.',
                    'material' => 'MDF, dəri',
                    'size' => '120x60 sm',
                ],
                'en' => [
                    'title' => 'Bedroom Desk and Chair',
                    'description' => 'Comfortable desk set for bedrooms.',
                    'seo_title' => 'Bedroom Desk',
                    'seo_keywords' => 'bedroom desk, bedroom',
                    'seo_description' => 'Comfortable and functional desk for bedrooms.',
                    'material' => 'MDF, leather',
                    'size' => '120x60 cm',
                ],
            ],
            // Qonaq otağı (23)
            [
                'category_id' => 23,
                'az' => [
                    'title' => 'Qonaq Otağı Masası',
                    'description' => 'Qonaq otaqları üçün dekorativ masa.',
                    'seo_title' => 'Qonaq Otağı Masası',
                    'seo_keywords' => 'qonaq otağı, dekorativ',
                    'seo_description' => 'Qonaq otaqları üçün dekorativ və funksional masa.',
                    'material' => 'taxta, şüşə',
                    'size' => '100x50 sm',
                ],
                'en' => [
                    'title' => 'Living Room Desk',
                    'description' => 'Decorative desk for living rooms.',
                    'seo_title' => 'Living Room Desk',
                    'seo_keywords' => 'living room, decorative',
                    'seo_description' => 'Decorative and functional desk for living rooms.',
                    'material' => 'wood, glass',
                    'size' => '100x50 cm',
                ],
            ],
            // Masa dəstləri (24)
            [
                'category_id' => 24,
                'az' => [
                    'title' => 'Masa və Kreslo Dəsti',
                    'description' => 'Tam masa və kreslo dəsti.',
                    'seo_title' => 'Masa Dəsti',
                    'seo_keywords' => 'masa dəsti, kreslo',
                    'seo_description' => 'Tam masa və kreslo dəsti.',
                    'material' => 'MDF, mesh',
                    'size' => '140x70 sm',
                ],
                'en' => [
                    'title' => 'Desk and Chair Set',
                    'description' => 'Complete desk and chair set.',
                    'seo_title' => 'Desk Set',
                    'seo_keywords' => 'desk set, chair',
                    'seo_description' => 'Complete desk and chair set.',
                    'material' => 'MDF, mesh',
                    'size' => '140x70 cm',
                ],
            ],
            // Mətbəx mebeli (25)
            [
                'category_id' => 25,
                'az' => [
                    'title' => 'Mətbəx Masası və Kreslolar',
                    'description' => 'Mətbəx üçün rahat masa dəsti.',
                    'seo_title' => 'Mətbəx Masası',
                    'seo_keywords' => 'mətbəx masası, yemək masası',
                    'seo_description' => 'Mətbəx üçün rahat və funksional masa dəsti.',
                    'material' => 'taxta, plastik',
                    'size' => '160x80 sm',
                ],
                'en' => [
                    'title' => 'Kitchen Table and Chairs',
                    'description' => 'Comfortable table set for kitchen.',
                    'seo_title' => 'Kitchen Table',
                    'seo_keywords' => 'kitchen table, dining table',
                    'seo_description' => 'Comfortable and functional table set for kitchen.',
                    'material' => 'wood, plastic',
                    'size' => '160x80 cm',
                ],
            ],
            // Oturacaqlar (26)
            [
                'category_id' => 26,
                'az' => [
                    'title' => 'Ofis Oturacağı',
                    'description' => 'Rahat ofis oturacağı.',
                    'seo_title' => 'Ofis Oturacağı',
                    'seo_keywords' => 'oturacaq, ofis',
                    'seo_description' => 'Rahat və funksional ofis oturacağı.',
                    'material' => 'dəri, metal',
                    'size' => 'Standart',
                ],
                'en' => [
                    'title' => 'Office Stool',
                    'description' => 'Comfortable office stool.',
                    'seo_title' => 'Office Stool',
                    'seo_keywords' => 'stool, office',
                    'seo_description' => 'Comfortable and functional office stool.',
                    'material' => 'leather, metal',
                    'size' => 'Standard',
                ],
            ],
            // Yumşaq mebellər (27)
            [
                'category_id' => 27,
                'az' => [
                    'title' => 'Yumşaq Kreslo və Masa',
                    'description' => 'Yumşaq mebellər dəsti.',
                    'seo_title' => 'Yumşaq Mebel',
                    'seo_keywords' => 'yumşaq mebel, kreslo',
                    'seo_description' => 'Rahat yumşaq mebellər dəsti.',
                    'material' => 'dəri, metal',
                    'size' => 'Standart',
                ],
                'en' => [
                    'title' => 'Soft Chair and Table',
                    'description' => 'Soft furniture set.',
                    'seo_title' => 'Soft Furniture',
                    'seo_keywords' => 'soft furniture, chair',
                    'seo_description' => 'Comfortable soft furniture set.',
                    'material' => 'leather, metal',
                    'size' => 'Standard',
                ],
            ],
            // TV stend (28)
            [
                'category_id' => 28,
                'az' => [
                    'title' => 'TV Stend və Masa',
                    'description' => 'TV üçün funksional stend.',
                    'seo_title' => 'TV Stend',
                    'seo_keywords' => 'TV stend, televizor',
                    'seo_description' => 'TV üçün funksional və dekorativ stend.',
                    'material' => 'MDF, şüşə',
                    'size' => '120x40 sm',
                ],
                'en' => [
                    'title' => 'TV Stand and Table',
                    'description' => 'Functional stand for TV.',
                    'seo_title' => 'TV Stand',
                    'seo_keywords' => 'TV stand, television',
                    'seo_description' => 'Functional and decorative stand for TV.',
                    'material' => 'MDF, glass',
                    'size' => '120x40 cm',
                ],
            ],
            // Qapılar (29)
            [
                'category_id' => 29,
                'az' => [
                    'title' => 'Ofis Qapısı',
                    'description' => 'Ofis üçün xüsusi qapı.',
                    'seo_title' => 'Ofis Qapısı',
                    'seo_keywords' => 'qapı, ofis',
                    'seo_description' => 'Ofis üçün xüsusi dizayn edilmiş qapı.',
                    'material' => 'taxta, şüşə',
                    'size' => '210x90 sm',
                ],
                'en' => [
                    'title' => 'Office Door',
                    'description' => 'Special door for office.',
                    'seo_title' => 'Office Door',
                    'seo_keywords' => 'door, office',
                    'seo_description' => 'Specially designed door for office.',
                    'material' => 'wood, glass',
                    'size' => '210x90 cm',
                ],
            ],
            // Kafe və restoran (30)
            [
                'category_id' => 30,
                'az' => [
                    'title' => 'Kafe Masası və Kreslolar',
                    'description' => 'Kafe üçün rahat masa dəsti.',
                    'seo_title' => 'Kafe Masası',
                    'seo_keywords' => 'kafe masası, restoran',
                    'seo_description' => 'Kafe və restoranlar üçün rahat masa dəsti.',
                    'material' => 'taxta, metal',
                    'size' => '80x80 sm',
                ],
                'en' => [
                    'title' => 'Cafe Table and Chairs',
                    'description' => 'Comfortable table set for cafe.',
                    'seo_title' => 'Cafe Table',
                    'seo_keywords' => 'cafe table, restaurant',
                    'seo_description' => 'Comfortable table set for cafes and restaurants.',
                    'material' => 'wood, metal',
                    'size' => '80x80 cm',
                ],
            ],
            // Otel mebelləri (31)
            [
                'category_id' => 31,
                'az' => [
                    'title' => 'Otel Masası və Kreslo',
                    'description' => 'Otel otaqları üçün lüks masa dəsti.',
                    'seo_title' => 'Otel Masası',
                    'seo_keywords' => 'otel masası, lüks',
                    'seo_description' => 'Otel otaqları üçün lüks və rahat masa dəsti.',
                    'material' => 'qaraşma, dəri',
                    'size' => '120x60 sm',
                ],
                'en' => [
                    'title' => 'Hotel Desk and Chair',
                    'description' => 'Luxury desk set for hotel rooms.',
                    'seo_title' => 'Hotel Desk',
                    'seo_keywords' => 'hotel desk, luxury',
                    'seo_description' => 'Luxury and comfortable desk set for hotel rooms.',
                    'material' => 'walnut, leather',
                    'size' => '120x60 cm',
                ],
            ],
            // Bank mebelləri (32)
            [
                'category_id' => 32,
                'az' => [
                    'title' => 'Bank Masası və Kreslo',
                    'description' => 'Bank üçün xüsusi masa dəsti.',
                    'seo_title' => 'Bank Masası',
                    'seo_keywords' => 'bank masası, maliyyə',
                    'seo_description' => 'Bank və maliyyə müəssisələri üçün xüsusi masa.',
                    'material' => 'MDF, metal',
                    'size' => '140x70 sm',
                ],
                'en' => [
                    'title' => 'Bank Desk and Chair',
                    'description' => 'Special desk set for bank.',
                    'seo_title' => 'Bank Desk',
                    'seo_keywords' => 'bank desk, finance',
                    'seo_description' => 'Special desk for banks and financial institutions.',
                    'material' => 'MDF, metal',
                    'size' => '140x70 cm',
                ],
            ],
            // Gözləmə zonası (33)
            [
                'category_id' => 33,
                'az' => [
                    'title' => 'Gözləmə Zonası Masa və Kreslolar',
                    'description' => 'Gözləmə zonası üçün rahat mebellər.',
                    'seo_title' => 'Gözləmə Zonası',
                    'seo_keywords' => 'gözləmə, zona, kreslo',
                    'seo_description' => 'Gözləmə zonası üçün rahat və funksional mebellər.',
                    'material' => 'dəri, metal',
                    'size' => 'Standart',
                ],
                'en' => [
                    'title' => 'Waiting Area Table and Chairs',
                    'description' => 'Comfortable furniture for waiting area.',
                    'seo_title' => 'Waiting Area',
                    'seo_keywords' => 'waiting, area, chair',
                    'seo_description' => 'Comfortable and functional furniture for waiting area.',
                    'material' => 'leather, metal',
                    'size' => 'Standard',
                ],
            ],
            // Qeydiyyat zonası (34)
            [
                'category_id' => 34,
                'az' => [
                    'title' => 'Qeydiyyat Zonası Masa və Kreslo',
                    'description' => 'Qeydiyyat zonası üçün funksional masa.',
                    'seo_title' => 'Qeydiyyat Zonası',
                    'seo_keywords' => 'qeydiyyat, zona, masa',
                    'seo_description' => 'Qeydiyyat zonası üçün funksional və rahat masa.',
                    'material' => 'MDF, metal',
                    'size' => '100x50 sm',
                ],
                'en' => [
                    'title' => 'Registration Area Desk and Chair',
                    'description' => 'Functional desk for registration area.',
                    'seo_title' => 'Registration Area',
                    'seo_keywords' => 'registration, area, desk',
                    'seo_description' => 'Functional and comfortable desk for registration area.',
                    'material' => 'MDF, metal',
                    'size' => '100x50 cm',
                ],
            ],
            // Uşaq mebelləri (35)
            [
                'category_id' => 35,
                'az' => [
                    'title' => 'Uşaq Masası və Kreslo',
                    'description' => 'Uşaqlar üçün rahat və təhlükəsiz masa.',
                    'seo_title' => 'Uşaq Masası',
                    'seo_keywords' => 'uşaq masası, uşaq mebeli',
                    'seo_description' => 'Uşaqlar üçün rahat və təhlükəsiz masa dəsti.',
                    'material' => 'MDF, plastik',
                    'size' => '80x50 sm',
                ],
                'en' => [
                    'title' => 'Children Desk and Chair',
                    'description' => 'Comfortable and safe desk for children.',
                    'seo_title' => 'Children Desk',
                    'seo_keywords' => 'children desk, kids furniture',
                    'seo_description' => 'Comfortable and safe desk set for children.',
                    'material' => 'MDF, plastic',
                    'size' => '80x50 cm',
                ],
            ],
            // Məktəb mebeli (36)
            [
                'category_id' => 36,
                'az' => [
                    'title' => 'Məktəb Masası və Kreslo',
                    'description' => 'Məktəb üçün funksional masa dəsti.',
                    'seo_title' => 'Məktəb Masası',
                    'seo_keywords' => 'məktəb masası, təhsil',
                    'seo_description' => 'Məktəb və təhsil müəssisələri üçün funksional masa.',
                    'material' => 'MDF, metal',
                    'size' => '120x60 sm',
                ],
                'en' => [
                    'title' => 'School Desk and Chair',
                    'description' => 'Functional desk set for school.',
                    'seo_title' => 'School Desk',
                    'seo_keywords' => 'school desk, education',
                    'seo_description' => 'Functional desk for schools and educational institutions.',
                    'material' => 'MDF, metal',
                    'size' => '120x60 cm',
                ],
            ],
            // Dolablar (37)
            [
                'category_id' => 37,
                'az' => [
                    'title' => 'Ofis Dolabı',
                    'description' => 'Ofis üçün funksional dolab.',
                    'seo_title' => 'Ofis Dolabı',
                    'seo_keywords' => 'dolab, ofis, saxlama',
                    'seo_description' => 'Ofis üçün funksional və geniş dolab.',
                    'material' => 'MDF, metal',
                    'size' => '200x60x180 sm',
                ],
                'en' => [
                    'title' => 'Office Cabinet',
                    'description' => 'Functional cabinet for office.',
                    'seo_title' => 'Office Cabinet',
                    'seo_keywords' => 'cabinet, office, storage',
                    'seo_description' => 'Functional and spacious cabinet for office.',
                    'material' => 'MDF, metal',
                    'size' => '200x60x180 cm',
                ],
            ],
            // Rəflər (38)
            [
                'category_id' => 38,
                'az' => [
                    'title' => 'Ofis Rəfi',
                    'description' => 'Ofis üçün funksional rəf sistemi.',
                    'seo_title' => 'Ofis Rəfi',
                    'seo_keywords' => 'rəf, ofis, saxlama',
                    'seo_description' => 'Ofis üçün funksional və modern rəf sistemi.',
                    'material' => 'MDF, metal',
                    'size' => '120x30x180 sm',
                ],
                'en' => [
                    'title' => 'Office Shelf',
                    'description' => 'Functional shelf system for office.',
                    'seo_title' => 'Office Shelf',
                    'seo_keywords' => 'shelf, office, storage',
                    'seo_description' => 'Functional and modern shelf system for office.',
                    'material' => 'MDF, metal',
                    'size' => '120x30x180 cm',
                ],
            ],
            // Stellajlar (39)
            [
                'category_id' => 39,
                'az' => [
                    'title' => 'Ofis Stellajı',
                    'description' => 'Ofis üçün funksional stellaj.',
                    'seo_title' => 'Ofis Stellajı',
                    'seo_keywords' => 'stellaj, ofis, saxlama',
                    'seo_description' => 'Ofis üçün funksional və geniş stellaj.',
                    'material' => 'MDF, metal',
                    'size' => '150x40x200 sm',
                ],
                'en' => [
                    'title' => 'Office Rack',
                    'description' => 'Functional rack for office.',
                    'seo_title' => 'Office Rack',
                    'seo_keywords' => 'rack, office, storage',
                    'seo_description' => 'Functional and spacious rack for office.',
                    'material' => 'MDF, metal',
                    'size' => '150x40x200 cm',
                ],
            ],
            // Zibil qutuları (40)
            [
                'category_id' => 40,
                'az' => [
                    'title' => 'Ofis Zibil Qutusu',
                    'description' => 'Ofis üçün funksional zibil qutusu.',
                    'seo_title' => 'Zibil Qutusu',
                    'seo_keywords' => 'zibil qutusu, ofis',
                    'seo_description' => 'Ofis üçün funksional və təmiz zibil qutusu.',
                    'material' => 'plastik, metal',
                    'size' => '40x30x50 sm',
                ],
                'en' => [
                    'title' => 'Office Waste Bin',
                    'description' => 'Functional waste bin for office.',
                    'seo_title' => 'Waste Bin',
                    'seo_keywords' => 'waste bin, office',
                    'seo_description' => 'Functional and clean waste bin for office.',
                    'material' => 'plastic, metal',
                    'size' => '40x30x50 cm',
                ],
            ],
            // Asılqanlar (41)
            [
                'category_id' => 41,
                'az' => [
                    'title' => 'Ofis Asılqanı',
                    'description' => 'Ofis üçün funksional asılqan.',
                    'seo_title' => 'Ofis Asılqanı',
                    'seo_keywords' => 'asılqan, ofis',
                    'seo_description' => 'Ofis üçün funksional və modern asılqan.',
                    'material' => 'metal, plastik',
                    'size' => 'Standart',
                ],
                'en' => [
                    'title' => 'Office Hanger',
                    'description' => 'Functional hanger for office.',
                    'seo_title' => 'Office Hanger',
                    'seo_keywords' => 'hanger, office',
                    'seo_description' => 'Functional and modern hanger for office.',
                    'material' => 'metal, plastic',
                    'size' => 'Standard',
                ],
            ],
            // Jurnal masaları (42)
            [
                'category_id' => 42,
                'az' => [
                    'title' => 'Jurnal Masası',
                    'description' => 'Jurnallar üçün xüsusi masa.',
                    'seo_title' => 'Jurnal Masası',
                    'seo_keywords' => 'jurnal masası, qəzet',
                    'seo_description' => 'Jurnallar və qəzetlər üçün xüsusi masa.',
                    'material' => 'MDF, şüşə',
                    'size' => '60x40 sm',
                ],
                'en' => [
                    'title' => 'Magazine Table',
                    'description' => 'Special table for magazines.',
                    'seo_title' => 'Magazine Table',
                    'seo_keywords' => 'magazine table, newspaper',
                    'seo_description' => 'Special table for magazines and newspapers.',
                    'material' => 'MDF, glass',
                    'size' => '60x40 cm',
                ],
            ],
            // Pufiklər (43)
            [
                'category_id' => 43,
                'az' => [
                    'title' => 'Ofis Pufiki',
                    'description' => 'Ofis üçün rahat pufik.',
                    'seo_title' => 'Ofis Pufiki',
                    'seo_keywords' => 'pufik, ofis, oturacaq',
                    'seo_description' => 'Ofis üçün rahat və funksional pufik.',
                    'material' => 'dəri, metal',
                    'size' => 'Standart',
                ],
                'en' => [
                    'title' => 'Office Pouf',
                    'description' => 'Comfortable pouf for office.',
                    'seo_title' => 'Office Pouf',
                    'seo_keywords' => 'pouf, office, seat',
                    'seo_description' => 'Comfortable and functional pouf for office.',
                    'material' => 'leather, metal',
                    'size' => 'Standard',
                ],
            ],
            // Dekorlar (44)
            [
                'category_id' => 44,
                'az' => [
                    'title' => 'Ofis Dekoru',
                    'description' => 'Ofis üçün dekorativ element.',
                    'seo_title' => 'Ofis Dekoru',
                    'seo_keywords' => 'dekor, ofis, dizayn',
                    'seo_description' => 'Ofis üçün dekorativ və funksional element.',
                    'material' => 'taxta, metal',
                    'size' => 'Standart',
                ],
                'en' => [
                    'title' => 'Office Decoration',
                    'description' => 'Decorative element for office.',
                    'seo_title' => 'Office Decoration',
                    'seo_keywords' => 'decoration, office, design',
                    'seo_description' => 'Decorative and functional element for office.',
                    'material' => 'wood, metal',
                    'size' => 'Standard',
                ],
            ],
            // Ümumi mebellər (45)
            [
                'category_id' => 45,
                'az' => [
                    'title' => 'Ümumi Masa və Kreslo',
                    'description' => 'Ümumi istifadə üçün masa dəsti.',
                    'seo_title' => 'Ümumi Masa',
                    'seo_keywords' => 'ümumi masa, mebel',
                    'seo_description' => 'Ümumi istifadə üçün rahat və funksional masa.',
                    'material' => 'MDF, metal',
                    'size' => '100x60 sm',
                ],
                'en' => [
                    'title' => 'General Desk and Chair',
                    'description' => 'Desk set for general use.',
                    'seo_title' => 'General Desk',
                    'seo_keywords' => 'general desk, furniture',
                    'seo_description' => 'Comfortable and functional desk for general use.',
                    'material' => 'MDF, metal',
                    'size' => '100x60 cm',
                ],
            ],
            // Səhiyyə mebelləri (46)
            [
                'category_id' => 46,
                'az' => [
                    'title' => 'Həkim Masası və Kreslo',
                    'description' => 'Səhiyyə müəssisələri üçün xüsusi masa.',
                    'seo_title' => 'Həkim Masası',
                    'seo_keywords' => 'həkim masası, səhiyyə',
                    'seo_description' => 'Səhiyyə müəssisələri üçün xüsusi və funksional masa.',
                    'material' => 'MDF, metal',
                    'size' => '140x70 sm',
                ],
                'en' => [
                    'title' => 'Doctor Desk and Chair',
                    'description' => 'Special desk for healthcare facilities.',
                    'seo_title' => 'Doctor Desk',
                    'seo_keywords' => 'doctor desk, healthcare',
                    'seo_description' => 'Special and functional desk for healthcare facilities.',
                    'material' => 'MDF, metal',
                    'size' => '140x70 cm',
                ],
            ],
            // Hərbi mebellər (47)
            [
                'category_id' => 47,
                'az' => [
                    'title' => 'Hərbi Masa və Kreslo',
                    'description' => 'Hərbi müəssisələr üçün xüsusi masa.',
                    'seo_title' => 'Hərbi Masa',
                    'seo_keywords' => 'hərbi masa, müdafiə',
                    'seo_description' => 'Hərbi müəssisələr üçün xüsusi və güclü masa.',
                    'material' => 'metal, plastik',
                    'size' => '120x60 sm',
                ],
                'en' => [
                    'title' => 'Military Desk and Chair',
                    'description' => 'Special desk for military facilities.',
                    'seo_title' => 'Military Desk',
                    'seo_keywords' => 'military desk, defense',
                    'seo_description' => 'Special and strong desk for military facilities.',
                    'material' => 'metal, plastic',
                    'size' => '120x60 cm',
                ],
            ],
            // Seyflər (48)
            [
                'category_id' => 48,
                'az' => [
                    'title' => 'Ofis Seyfi',
                    'description' => 'Ofis üçün təhlükəsiz seyf.',
                    'seo_title' => 'Ofis Seyfi',
                    'seo_keywords' => 'seyf, ofis, təhlükəsizlik',
                    'seo_description' => 'Ofis üçün təhlükəsiz və funksional seyf.',
                    'material' => 'metal, şüşə',
                    'size' => '60x40x80 sm',
                ],
                'en' => [
                    'title' => 'Office Safe',
                    'description' => 'Secure safe for office.',
                    'seo_title' => 'Office Safe',
                    'seo_keywords' => 'safe, office, security',
                    'seo_description' => 'Secure and functional safe for office.',
                    'material' => 'metal, glass',
                    'size' => '60x40x80 cm',
                ],
            ],
        ];

        for ($i = 0; $i < count($products); $i++) {
            $product = Product::create([
                'product_category_id' => $products[$i]['category_id'],
                'product_type_id' => 2, // Ümumi
                'discount_percent' => rand(0, 30),
                'status' => true,
            ]);

            // EN
            ProductTranslate::create([
                'product_id' => $product->id,
                'language_id' => 1,
                'title' => $products[$i]['en']['title'],
                'description' => $products[$i]['en']['description'],
                'slug' => Str::slug($products[$i]['en']['title']),
                'seo_title' => $products[$i]['en']['seo_title'],
                'seo_keywords' => $products[$i]['en']['seo_keywords'],
                'seo_description' => $products[$i]['en']['seo_description'],
                'weight' => rand(100, 200),
                'material' => $products[$i]['en']['material'],
                'size' => $products[$i]['en']['size'],
            ]);

            // AZ
            ProductTranslate::create([
                'product_id' => $product->id,
                'language_id' => 3,
                'title' => $products[$i]['az']['title'],
                'description' => $products[$i]['az']['description'],
                'slug' => Str::slug($products[$i]['az']['title']),
                'seo_title' => $products[$i]['az']['seo_title'],
                'seo_keywords' => $products[$i]['az']['seo_keywords'],
                'seo_description' => $products[$i]['az']['seo_description'],
                'weight' => rand(100, 200),
                'material' => $products[$i]['az']['material'],
                'size' => $products[$i]['az']['size'],
            ]);
        }
    }
}
