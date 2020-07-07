<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComicTypes extends Model
{
    protected $table = 'comictypes';
    const CREATED_AT = null;
    const UPDATED_AT = null;

    public function comic_detail() {
        return $this->hasMany('App\ComicDetail', 'comic_types_id', 'id');
    }
}
