<?php
namespace App\Repositories;

use App\Models\BecomeAInvestorAdvantage;
use App\Services\ImageService;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class BecomeAInvestorAdvantageRepository extends BaseRepository
{
    protected $imageService;

    public function __construct(public BecomeAInvestorAdvantage $advantage, ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getAdvantages()
    {
        return $this->GetData($this->advantage, ['translates.language'])->get();
    }

    public function createAdvantage(array $data, array $translations, $iconFile = null)
    {
        return DB::transaction(function () use ($data, $translations, $iconFile) {
            $iconPath = $this->handleImageUpload($iconFile, 'become-a-investor-advantages', $this->imageService);

            $advantage = $this->advantage->create([
                'icon' => $iconPath,
                'status' => $data['status'] ?? true,
            ]);

            foreach ($translations as $languageId => $translation) {
                $advantage->translates()->create([
                    'language_id' => $languageId,
                    'icon_alt_text' => trim($translation['icon_alt_text'] ?? ''),
                    'title' => trim($translation['title'] ?? ''),
                    'description' => trim($translation['description'] ?? ''),
                ]);
            }

            return $advantage->load('translates.language');
        });
    }

    public function updateAdvantage(BecomeAInvestorAdvantage $advantage, array $data, array $translations, $iconFile = null)
    {
        return DB::transaction(function () use ($advantage, $data, $translations, $iconFile) {
            $iconPath = $this->handleImageUpdate($advantage, $iconFile, 'become-a-investor-advantages', $this->imageService);
            
            if ($iconPath) {
                $advantage->icon = $iconPath;
            }

            $advantage->status = $data['status'] ?? true;
            $advantage->save();

            foreach ($translations as $languageId => $translation) {
                $advantage->translates()->updateOrCreate(
                    ['language_id' => $languageId],
                    [
                        'icon_alt_text' => trim($translation['icon_alt_text'] ?? ''),
                        'title' => trim($translation['title'] ?? ''),
                        'description' => trim($translation['description'] ?? ''),
                    ]
                );
            }

            return $advantage->load('translates.language');
        });
    }

    public function deleteAdvantage(BecomeAInvestorAdvantage $advantage)
    {
        return DB::transaction(function () use ($advantage) {
            $this->handleImageDelete($advantage->icon, $this->imageService);
            return $advantage->delete();
        });
    }

    public function reorderAdvantages(array $advantages)
    {
        return $this->UpdateOrder($this->advantage, $advantages);
    }

    public function deleteSelected(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $advantages = $this->advantage->whereIn('id', $ids)->get();
            
            foreach ($advantages as $advantage) {
                if ($advantage->icon) {
                    $this->imageService->delete($advantage->icon);
                }
            }
            
            return $this->advantage->whereIn('id', $ids)->delete();
        });
    }
}
