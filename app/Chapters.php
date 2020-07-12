<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapters extends Model
{
    protected $table = 'chapters';

    // Relations
    public function comics() {
        return $this->belongsTo('App\Comics', 'comics_id', 'id');
    }

    // Necessary function
    private function checkExist($comicId, $chapterName) {
        return !is_null(Chapters::where('comics_id', '=', $comicId)->where('name', '=', $chapterName)->first());
    }

    public function add($chapterName, $content, $comicId) {
        if (!$this->checkExist($comicId, $chapterName)) {
            $chapter = new Chapters;
            $chapter->name = $chapterName;
            $chapter->content = $content;
            $chapter->comics_id = $comicId;
            $chapter->save();
        }
    }
}
