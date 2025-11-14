<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneratePopupAdSeeder extends Command
{
    protected $signature = 'make:popupad-seeder';
    protected $description = 'Generate PopupAdSeeder with example demo data';

    public function handle()
    {
        $seederPath = database_path('seeders/PopupAdSeeder.php');

        $content = <<<EOT
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use App\\Models\\PopupAd;
use App\\Models\\PopupAdTranslate;

class PopupAdSeeder extends Seeder
{
    public function run(): void
    {
        // Create popup
        \$popup = PopupAd::create([
            'image' => 'popup_ads/example.webp',
            'order' => 0,
            'status' => 1,
        ]);

        // Translations
        \$translations = [
            [
                'language_id' => 1,
                'locale' => 'en',
                'title' => 'Welcome Offer',
                'description' => 'Get special discount on your first order!',
                'image_alt_text' => 'Welcome Popup Image',
            ],
            [
                'language_id' => 2,
                'locale' => 'az',
                'title' => 'Xoş Gəlmisiniz Təklifi',
                'description' => 'İlk sifarişinizdə xüsusi endirim qazanın!',
                'image_alt_text' => 'Popup Şəkli',
            ],
            [
                'language_id' => 3,
                'locale' => 'ru',
                'title' => 'Приветственное предложение',
                'description' => 'Получите скидку на ваш первый заказ!',
                'image_alt_text' => 'Изображение попапа',
            ],
        ];

        foreach (\$translations as \$tr) {
            PopupAdTranslate::create([
                'popup_ad_id' => \$popup->id,
                ...\$tr
            ]);
        }
    }
}
EOT;

        file_put_contents($seederPath, $content);

        $this->info("PopupAdSeeder created successfully: database/seeders/PopupAdSeeder.php");
    }
}
