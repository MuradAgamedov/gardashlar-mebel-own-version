<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProjectImageRepositoryInterface;
use App\Interfaces\Services\Admin\ProjectImageServiceInterface;
use App\Services\BaseService;

class ProjectImageService extends BaseService implements ProjectImageServiceInterface
{
    protected ProjectImageRepositoryInterface $ProjectImageRepository;

    public function __construct(ProjectImageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProjectImageRepository = $repository;
    }

    public function getOrderedImages($projectId)
    {
        return $this->ProjectImageRepository->getOrderedImages($projectId);
    }

    public function getMaxOrder($projectId)
    {
        return $this->ProjectImageRepository->getMaxOrder($projectId);
    }

    /**
     * Create multiple project images
     *
     * @param int $projectId
     * @param array|UploadedFile $images
     * @param bool $status
     * @return array
     */
    public function createImages($projectId, $images, $status = true)
    {
        return $this->ProjectImageRepository->createImages($projectId, $images, $status);
    }

    /**
     * Update a project image
     *
     * @param ProjectImage $image
     * @param array $data
     * @param UploadedFile|null $newImage
     * @return void
     */
    public function updateImage(\App\Models\ProjectImage $image, array $data, $newImage = null)
    {
        return $this->ProjectImageRepository->updateImage($image, $data, $newImage);
    }

    /**
     * Delete a project image
     *
     * @param ProjectImage $image
     * @return bool
     */
    public function deleteImage(\App\Models\ProjectImage $image)
    {
        return $this->ProjectImageRepository->deleteImage($image);
    }

    /**
     * Set main image for project
     *
     * @param int $projectId
     * @param int $imageId
     * @return void
     */
    public function setMainImage($projectId, $imageId)
    {
        return $this->ProjectImageRepository->setMainImage($projectId, $imageId);
    }

    /**
     * Reorder project images
     *
     * @param int $projectId
     * @param array $itemIds
     * @return bool
     */
    public function reorderImages($projectId, array $itemIds)
    {
        return $this->ProjectImageRepository->reorderImages($projectId, $itemIds);
    }
}
