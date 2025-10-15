<?php
namespace App\Repositories;

use App\Models\Certificate;
use App\Services\ImageService;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class CertificateRepository extends BaseRepository
{
    protected $imageService;

    public function __construct(public Certificate $certificate, ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getCertificates()
    {
        return $this->GetData($this->certificate, ['translates.language'])->get();
    }

    public function createCertificate(array $data, array $translations, $imageFile = null)
    {
        return DB::transaction(function () use ($data, $translations, $imageFile) {
            $imagePath = $this->handleImageUpload($imageFile, 'certificates', $this->imageService);

            $certificate = $this->certificate->create([
                'image' => $imagePath,
                'date' => $data['date'],
                'status' => $data['status'] ?? true,
            ]);

            foreach ($translations as $languageId => $translation) {
                $certificate->translates()->create([
                    'language_id' => $languageId,
                    'image_alt_text' => trim($translation['image_alt_text'] ?? ''),
                    'title' => trim($translation['title'] ?? ''),
                ]);
            }

            return $certificate->load('translates.language');
        });
    }

    public function updateCertificate(Certificate $certificate, array $data, array $translations, $imageFile = null)
    {
        return DB::transaction(function () use ($certificate, $data, $translations, $imageFile) {
            $imagePath = $this->handleImageUpdate($certificate, $imageFile, 'certificates', $this->imageService);
            
            if ($imagePath) {
                $certificate->image = $imagePath;
            }

            $certificate->date = $data['date'];
            $certificate->status = $data['status'] ?? true;
            $certificate->save();

            foreach ($translations as $languageId => $translation) {
                $certificate->translates()->updateOrCreate(
                    ['language_id' => $languageId],
                    [
                        'image_alt_text' => trim($translation['image_alt_text'] ?? ''),
                        'title' => trim($translation['title'] ?? ''),
                    ]
                );
            }

            return $certificate->load('translates.language');
        });
    }

    public function deleteCertificate(Certificate $certificate)
    {
        return DB::transaction(function () use ($certificate) {
            $this->handleImageDelete($certificate->image, $this->imageService);
            return $certificate->delete();
        });
    }

    public function reorderCertificates(array $certificates)
    {
        return $this->UpdateOrder($this->certificate, $certificates);
    }
}
