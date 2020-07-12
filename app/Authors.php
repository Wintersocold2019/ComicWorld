<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authors extends Model
{
    protected $table = 'authors';
    const CREATED_AT = null;
    const UPDATED_AT = null;

    // Relations
    public function comics() {
        return $this->hasMany('App\Comics', 'authors_id', 'id');
    }

    // Necessary function
    private function checkExist($authorName) {
        return !is_null(Authors::where('name', '=', $authorName)->first());
    }

    private function add($authorName) {
        $author       = new Authors;
        $author->name = $authorName;
        $author->save();
    }

    public function getId($authorName) {
        if (!$this->checkExist($authorName)) {
            $this->add($authorName);
        } 
        
        return Authors::where('name', $authorName)->first()->id;
    }  
}
