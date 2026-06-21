<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProjectImageServiceInterface extends BaseServiceInterface
{
    //

    public function getOrderedImages($projectId);

    public function getMaxOrder($projectId);

    /**
     * Create multiple project images
     *
     * @param int $projectId
     * @param array|UploadedFile $images
     * @param bool $status
     * @return array
     */
    public function createImages($projectId, $images, $status = true);

    /**
     * Update a project image
     *
     * @param ProjectImage $image
     * @param array $data
     * @param UploadedFile|null $newImage
     * @return void
     */
    public function updateImage(\App\Models\ProjectImage $image, array $data, $newImage = null);

    /**
     * Delete a project image
     *
     * @param ProjectImage $image
     * @return bool
     */
    public function deleteImage(\App\Models\ProjectImage $image);

    /**
     * Set main image for project
     *
     * @param int $projectId
     * @param int $imageId
     * @return void
     */
    public function setMainImage($projectId, $imageId);

    /**
     * Reorder project images
     *
     * @param int $projectId
     * @param array $itemIds
     * @return bool
     */
    public function reorderImages($projectId, array $itemIds);
}
