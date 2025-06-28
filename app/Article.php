<?php
// app/Article.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'status', 
        'create_by', 'update_by', 'delete_by',
        'twitter_id', 'source', 'twitter_data', 'featured_image'
    ];

    protected $casts = [
        'twitter_data' => 'array'
    ];

    public function categories(){
        return $this->belongsToMany('App\Category')->withPivot('category_id');
    }
    
    public function isFromTwitter()
    {
        return $this->source === 'TWITTER';
    }
    
    public function getTwitterUrlAttribute()
    {
        if ($this->twitter_id) {
            $username = config('services.twitter.username');
            return "https://twitter.com/{$username}/status/{$this->twitter_id}";
        }
        return null;
    }
}