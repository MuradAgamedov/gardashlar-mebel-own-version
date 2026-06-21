<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\NewsPostRepositoryInterface;
use App\Models\NewsPost;
use App\Models\NewsPostTranslate;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class NewsPostRepository extends BaseRepository implements NewsPostRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(NewsPost $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getPosts($request)
    {
        $query = $this->model->newQuery()->with(['category.translates', 'translates.language']);

        if ($request->filled('category_id')) {
            $query->where('news_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('translates', function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('card_description', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function createPost(array $data, array $translations, $cardImage = null, $innerBannerImage = null)
    {
        return DB::transaction(function () use ($data, $translations, $cardImage, $innerBannerImage) {
            if ($cardImage) {
                $data['card_image'] = $this->imageService->upload($cardImage, 'news-posts');
            }

            if ($innerBannerImage) {
                $data['inner_banner_image'] = $this->imageService->upload($innerBannerImage, 'news-posts');
            }

            $newsPost = $this->model->newQuery()->create($data);

            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    NewsPostTranslate::create(array_merge(
                        ['news_post_id' => $newsPost->id, 'language_id' => $languageId],
                        $translation
                    ));
                }
            }

            return $newsPost->load(['category.translates', 'translates.language']);
        });
    }

    public function updatePost(NewsPost $newsPost, array $data, array $translations, $cardImage = null, $innerBannerImage = null)
    {
        return DB::transaction(function () use ($newsPost, $data, $translations, $cardImage, $innerBannerImage) {
            if ($cardImage) {
                if ($newsPost->card_image) {
                    $this->imageService->delete($newsPost->card_image);
                }
                $data['card_image'] = $this->imageService->upload($cardImage, 'news-posts');
            }

            if ($innerBannerImage) {
                if ($newsPost->inner_banner_image) {
                    $this->imageService->delete($newsPost->inner_banner_image);
                }
                $data['inner_banner_image'] = $this->imageService->upload($innerBannerImage, 'news-posts');
            }

            $newsPost->update($data);

            foreach ($translations as $languageId => $translation) {
                if (!empty($translation['title'])) {
                    NewsPostTranslate::updateOrCreate(
                        ['news_post_id' => $newsPost->id, 'language_id' => $languageId],
                        $translation
                    );
                }
            }

            return $newsPost->load(['category.translates', 'translates.language']);
        });
    }

    public function deletePost(NewsPost $newsPost)
    {
        return DB::transaction(function () use ($newsPost) {
            if ($newsPost->card_image) {
                $this->imageService->delete($newsPost->card_image);
            }
            if ($newsPost->inner_banner_image) {
                $this->imageService->delete($newsPost->inner_banner_image);
            }
            return $newsPost->delete();
        });
    }

    public function bulkDelete(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $newsPosts = $this->model->newQuery()->whereIn('id', $ids)->get();

            foreach ($newsPosts as $newsPost) {
                if ($newsPost->card_image) {
                    $this->imageService->delete($newsPost->card_image);
                }
                if ($newsPost->inner_banner_image) {
                    $this->imageService->delete($newsPost->inner_banner_image);
                }
            }

            return $this->model->newQuery()->whereIn('id', $ids)->delete();
        });
    }
}
