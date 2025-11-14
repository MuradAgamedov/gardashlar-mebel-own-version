<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PopupAd;
use App\Models\PopupAdTranslate;

class PopupAdSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Popup Ad yarat
        $popup = PopupAd::create([
            'image'  => 'popup_ads/example.webp', // storage/app/public/popup_ads/example.webp
            'order'  => 0,
            'status' => 1,
        ]);

        // 2) Dillər üzrə tərcümələr
        $translations = [
            [
                'language_id'   => 1, // ENGLISH
                'locale'        => 'en',
                'title'         => 'Welcome Offer',
                'description'   => 'Get special discounts on your first order!',
                'image_alt_text'=> 'Popup image alt text EN',
            ],
            [
                'language_id'   => 3, // AZƏRBAYCAN
                'locale'        => 'az',
                'title'         => 'Xoş gəldin təklifi',
                'description'   => 'İlk sifarişinizdə xüsusi endirim qazanın!',
                'image_alt_text'=> 'Popup şəkil alt mətni AZ',
            ],
            [
                'language_id'   => 4, // RUS
                'locale'        => 'ru',
                'title'         => 'Приветственное предложение',
                'description'   => 'Получите скидку на ваш первый заказ!',
                'image_alt_text'=> 'Popup изображение alt RU',
            ],
        ];

        foreach ($translations as $tr) {
            PopupAdTranslate::create([
                'popup_ad_id'   => $popup->id,
                'language_id'   => $tr['language_id'],
                'locale'        => $tr['locale'],
                'title'         => $tr['title'],
                'description'   => $tr['description'],
                'image_alt_text'=> $tr['image_alt_text'],
            ]);
        }
    }
}
