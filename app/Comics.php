<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comics extends Model
{
    protected $table = 'comics';

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
}
