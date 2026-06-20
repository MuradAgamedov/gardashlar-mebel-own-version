<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Project;

class ProjectDetailResource extends JsonResource
{
    public int $languageId = 0;

    public function toArray(Request $request): array
    {
        $translation = $this->translates->firstWhere('language_id', $this->languageId);
        $categoryTranslation = $this->category?->translates?->firstWhere('language_id', $this->languageId);

        $slugTranslations = $this->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);
        $categorySlugTranslations = $this->category?->translates?->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]) ?? collect();

        $similarProjects = Project::with([
            'translates' => fn($q) => $q->where('language_id', $this->languageId),
            'category.translates' => fn($q) => $q->where('language_id', $this->languageId),
            'images' => fn($q) => $q->where('is_main_image', true)->where('status', true),
        ])
        ->where('project_category_id', $this->project_category_id)
        ->where('id', '!=', $this->id)
        ->active()
        ->ordered()
        ->limit(4)
        ->get()
        ->map(function ($project) {
            $tr = $project->translates->first();
            $catTr = $project->category?->translates?->first();
            $image = $project->images->first();
            return [
                'id' => $project->id,
                'title' => $tr?->title,
                'slug' => $tr?->slug,
                'company_name' => $tr?->company_name,
                'category_slug' => $catTr?->slug,
                'category_title' => $catTr?->title,
                'main_image' => $image?->image_url ?? asset('images/default_image.webp'),
            ];
        });

        return [
            'id' => $this->id,
            'title' => $translation?->title,
            'slug' => $translation?->slug,
            'company_name' => $translation?->company_name,
            'description' => $translation?->description,
            'seo_title' => $translation?->seo_title,
            'seo_keywords' => $translation?->seo_keywords,
            'seo_description' => $translation?->seo_description,
            'category_slug' => $categoryTranslation?->slug,
            'category_title' => $categoryTranslation?->title,
            'images' => $this->images->map(fn($image) => [
                'id' => $image->id,
                'image_url' => $image->image_url,
                'is_main_image' => $image->is_main_image,
            ]),
            'videos' => $this->videos->map(fn($video) => [
                'id' => $video->id,
                'video_link' => $video->video_link,
                'video_id' => explode('?', $video->video_link)[1] ?? null,
            ]),
            'similar_projects' => $similarProjects,
            'translates' => [
                'slug' => $slugTranslations,
                'category_slug' => $categorySlugTranslations,
            ],
        ];
    }
}
