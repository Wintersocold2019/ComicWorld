<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComicDetail extends Model
{
    protected $table = 'comicdetail';

    public function comic_types() {
        return $this->belongsTo('App\ComicTypes', 'comic_types_id', 'comic_types_id');
    }

    public function comics() {
        return $this->belongsTo('App\Comics', 'comics_id', 'comics_id');
    }
}
