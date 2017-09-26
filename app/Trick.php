<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trick extends Model
{
    protected $table = 'tricks';

    protected $primaryKey = 'trick_id';

    protected $fillable = ['category_id', 'trick_name', 'tags', 'ld_video_url', 'hd_video_url', 'trick_description'];

    /**
     * Get the Category that owns the Trick.
     */
    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    /**
     * Get User that try tricks
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'dribblers', 'trick_id', 'user_id')->withPivot('try_on');
    }

    /**
     * Get Video
     */
    public function videos()
    {
        return $this->hasMany('App\Video', 'trick_id', 'trick_id');
    }
}
