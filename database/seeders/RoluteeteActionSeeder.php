<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoluteeteAction;
use App\Models\RoluteeteActionTranslate;
use App\Models\Language;

class RoluteeteActionSeeder extends Seeder
{
    public function run(): void
    {
        // nümunə üçün dilləri götürək
        $languages = Language::all();

        // əsas action yaradılır
        $action = RoluteeteAction::create([
            'image' => 'uploads/roluteete_actions/sample.jpg',
            'percent' => 20,
            'status' => true,
        ]);

        // hər dil üçün tərcümə yaradırıq
        foreach ($languages as $lang) {
            RoluteeteActionTranslate::create([
                'roluteete_action_id' => $action->id,
                'language_id' => $lang->id,
                'title' => "Sample title ({$lang->lang_code})",
                'type' => 'percent',
            ]);
        }

        // əlavə nümunə üçün 2-ci action
        $action2 = RoluteeteAction::create([
            'image' => 'uploads/roluteete_actions/sample2.jpg',
            'percent' => 50,
            'status' => false,
        ]);

        foreach ($languages as $lang) {
            RoluteeteActionTranslate::create([
                'roluteete_action_id' => $action2->id,
                'language_id' => $lang->id,
                'title' => "Another title ({$lang->lang_code})",
                'type' => 'percent',
            ]);
        }
    }
}
