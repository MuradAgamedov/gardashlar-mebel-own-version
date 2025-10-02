<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoluteeteAction;
use App\Models\RoluteeteActionTranslate;

class RoluteeteActionSeeder extends Seeder
{
    public function run(): void
    {
        $actions = [
            ['image' => 'uploads/roluteete_actions/discount.png', 'percent' => 10, 'status' => 1, 'title' => ['az' => 'Endirim', 'en' => 'Discount', 'ru' => 'Скидка']],
            ['image' => 'uploads/roluteete_actions/summer.png',   'percent' => 15, 'status' => 1, 'title' => ['az' => 'Yay kampaniyası', 'en' => 'Summer campaign', 'ru' => 'Летняя акция']],
            ['image' => 'uploads/roluteete_actions/winter.png',   'percent' => 20, 'status' => 1, 'title' => ['az' => 'Qış kampaniyası', 'en' => 'Winter campaign', 'ru' => 'Зимняя акция']],
            ['image' => 'uploads/roluteete_actions/student.png',  'percent' => 25, 'status' => 1, 'title' => ['az' => 'Tələbə endirimi', 'en' => 'Student discount', 'ru' => 'Студенческая скидка']],
            ['image' => 'uploads/roluteete_actions/vip.png',      'percent' => 30, 'status' => 1, 'title' => ['az' => 'VIP Aksiya', 'en' => 'VIP Campaign', 'ru' => 'VIP акция']],
            ['image' => 'uploads/roluteete_actions/weekend.png',  'percent' => 40, 'status' => 1, 'title' => ['az' => 'Həftəsonu endirimi', 'en' => 'Weekend discount', 'ru' => 'Скидка на выходных']],
            ['image' => 'uploads/roluteete_actions/holiday.png',  'percent' => 50, 'status' => 1, 'title' => ['az' => 'Bayram kampaniyası', 'en' => 'Holiday campaign', 'ru' => 'Праздничная акция']],
        ];

        foreach ($actions as $data) {
            $action = RoluteeteAction::create([
                'image'   => $data['image'],
                'percent' => $data['percent'],
                'status'  => $data['status'],
            ]);

            // manual olaraq dillərə görə tərcümə
            RoluteeteActionTranslate::create([
                'roluteete_action_id' => $action->id,
                'language_id' => 1, // AZ
                'title' => $data['title']['az'],
                'type'  => 'percent',
            ]);
            RoluteeteActionTranslate::create([
                'roluteete_action_id' => $action->id,
                'language_id' => 2, // EN
                'title' => $data['title']['en'],
                'type'  => 'percent',
            ]);
            RoluteeteActionTranslate::create([
                'roluteete_action_id' => $action->id,
                'language_id' => 3, // RU
                'title' => $data['title']['ru'],
                'type'  => 'percent',
            ]);
        }
    }
}
