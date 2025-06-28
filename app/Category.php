<?php
// app/Category.php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'create_by', 'update_by', 'delete_by'
    ];

    public function articles(){
        return $this->belongsToMany('App\Article');
    }
}