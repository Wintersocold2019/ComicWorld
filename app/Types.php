<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Types extends Model
{
    protected $table = 'types';

    public function comics() {
        return $this->hasMany('App\Comics', 'types_id', 'id');
    }
}
