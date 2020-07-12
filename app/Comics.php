<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comics extends Model
{
    protected $table = 'comics';

    // Relations
    public function chapters() {
        return $this->hasMany('App\Chapters', 'comics_id', 'id');
    }

    public function comic_detail() {
        return $this->hasManny('App\ComicDetail', 'comics_id', 'id');
    }

    public function status_types() {
        return $this->belongsTo('App\StatusTypes', 'status_types_id', 'id');
    }

    public function types() {
        return $this->belongsTo('App\Types', 'types_id', 'id');
    }

    public function authors() {
        return $this->belongsTo('App\Authors', 'authors_id', 'id');
    }

    // Necessary function
    private function checkExist($comicName) {
        return !is_null(Comics::where('name', '=', $comicName)->first());
    }

    public function getId($comicName) {
        return Comics::where('name', $comicName)->first()->id;
    }

    public function add($comicName, $href, $description, $numOfChaps, $numOfViews, $source, $image, $authorId, $typeId, $statusTypeId) {
        if (!$this->checkExist($comicName)) {
            $comic = new Comics;
            $comic->name = $comicName;
            $comic->href = $href;
            $comic->description = $description;
            $comic->numOfChaps = $numOfChaps;
            $comic->numOfViews = $numOfViews;
            $comic->source = $source;
            $comic->image = $image;
            $comic->authors_id = $authorId;
            $comic->types_id = $typeId;
            $comic->status_types_id = $statusTypeId;
            $comic->save();
        }
    }
}
