<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComicTypes extends Model
{
    protected $table = 'comic_types';
    const CREATED_AT = null;
    const UPDATED_AT = null;

    // Relations
    public function comic_detail() {
        return $this->hasMany('App\ComicDetail', 'comic_types_id', 'id');
    }

    // Necessary function
    private function checkExist($comicTypeName) {
        return !is_null(ComicTypes::where('name', '=', $comicTypeName)->first());
    }

    public function add($comicTypeName, $href) {
        if (!$this->checkExist($comicTypeName)) {
            $comicType = new ComicTypes;
            $comicType->name = $comicTypeName;
            $comicType->href = $href;
            $comicType->save();
        }
    }

    public function getId($comicTypeName) {
        return ComicTypes::where('name', $comicTypeName)->first()->id;
    } 
}
