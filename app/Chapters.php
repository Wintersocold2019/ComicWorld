<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chapters extends Model
{
    protected $table = 'chapters';

    public function comics() {
        return $this->belongsTo('App\Comics', 'comics_id', 'id');
    }
}
