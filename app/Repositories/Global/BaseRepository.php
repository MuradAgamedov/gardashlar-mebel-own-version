<?php
namespace App\Repositories\Global;

abstract class BaseRepository {
    public function GetData($model, $with  = []) {
        return $model::with($with)->ordered();
    }
}