<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComicDetail extends Model
{
    protected $table = 'comic_detail';
    const CREATED_AT = null;
    const UPDATED_AT = null;

    // Relations
    public function comic_types() {
        return $this->belongsTo('App\ComicTypes', 'comic_types_id', 'comic_types_id');
    }

    public function comics() {
        return $this->belongsTo('App\Comics', 'comics_id', 'comics_id');
    }

    // Necessary function
    private function checkExist($comicId, $comicTypeId) {
        return !is_null(ComicDetail::where([['comics_id', '=', $comicId],['comic_types_id', '=', $comicTypeId]])->first());
    }

    public function add($comicId, $comicTypeId) {
        if (!$this->checkExist($comicId, $comicTypeId)) {
            $comicDetail = new ComicDetail;
        $comicDetail->comics_id = $comicId;
        $comicDetail->comic_types_id = $comicTypeId;
        $comicDetail->save();
        }
    }
}
