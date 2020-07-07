<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusTypes extends Model
{
    protected $table = 'statustypes';

    public function comics() {
        return $this->hasMany('App\Comics', 'status_types_id', 'id');
    }
}
