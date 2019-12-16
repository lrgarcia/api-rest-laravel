<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    //Uno a muchos relacion
    public function posts(){
      return $this->hasMany('App\Post');  
}
    
}
