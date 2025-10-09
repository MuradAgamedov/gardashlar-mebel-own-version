<?php

namespace App\Repositories;

use App\Models\ProjectVideo;
use App\Repositories\Global\BaseRepository;
use Illuminate\Support\Facades\DB;

class ProjectVideoRepository extends BaseRepository
{
    public function __construct(public ProjectVideo $projectVideo)
    {
    }

    public function getVideosByProject($projectId)
    {
        return $this->projectVideo->where('project_id', $projectId)
            ->ordered()
            ->get();
    }

    public function createVideo($projectId, array $data)
    {
        return DB::transaction(function () use ($projectId, $data) {
            return $this->projectVideo->create([
                'project_id' => $projectId,
                'video_link' => $data['video_link'],
                'status' => $data['status'] ?? false,
            ]);
        });
    }

    public function updateVideo($video, array $data)
    {
        return DB::transaction(function () use ($video, $data) {
            $video->update([
                'video_link' => $data['video_link'],
                'status' => $data['status'] ?? false,
            ]);
            return $video;
        });
    }

    public function deleteVideo($video)
    {
        return DB::transaction(function () use ($video) {
            return $video->delete();
        });
    }

    public function reorderVideos($itemIds)
    {
        return DB::transaction(function () use ($itemIds) {
            foreach ($itemIds as $index => $id) {
                $this->projectVideo->where('id', $id)->update(['order' => $index + 1]);
            }
            return true;
        });
    }
}
