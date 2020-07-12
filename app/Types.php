<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Types extends Model
{
    protected $table = 'types';
    const CREATED_AT = null;
    const UPDATED_AT = null;

    // Relations
    public function comics() {
        return $this->hasMany('App\Comics', 'types_id', 'id');
    }

    // Necessary function
    private function checkExist($type) {
        return !is_null(Types::where('name', '=', $type)->first());
    }

    private function add($type) {
        $typeItem       = new Types;
        $typeItem->name = $type;
        $typeItem->save();
    }

    public function getId($type) {
        if (!$this->checkExist($type)) {
            $this->add($type);
        } 
        
        return Types::where('name', $type)->first()->id;
    }
}
