<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusTypes extends Model
{
    protected $table = 'status_types';
    const CREATED_AT = null;
    const UPDATED_AT = null;

    // Relations
    public function comics() {
        return $this->hasMany('App\Comics', 'status_types_id', 'id');
    }

    // Necessary function
    private function checkExist($statusType) {
        return !is_null(StatusTypes::where('name', '=', $statusType)->first());
    }

    private function add($statusType) {
        $statusTypeItem       = new StatusTypes;
        $statusTypeItem->name = $statusType;
        $statusTypeItem->save();
    }

    public function getId($statusType) {
        if (!$this->checkExist($statusType)) {
            $this->add($statusType);
        } 

        return StatusTypes::where('name', $statusType)->first()->id;
    } 
}
