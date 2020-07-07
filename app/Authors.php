<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authors extends Model
{
    protected $table = 'authors';

    public function comics() {
        return $this->hasMany('App\Comics', 'authors_id', 'id');
    }
}
