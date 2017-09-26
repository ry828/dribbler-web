<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos';

    protected $primaryKey = 'video_id';

    protected $fillable = ['user_id', 'trick_id', 'thumbnail', 'url', 'hd_url', 'likes', 'views', 'comments', 'commentators'];

    /**
     * Get the user that owns the video
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the trick that owns the video
     */
    public function trick()
    {
        return $this->belongsTo('App\Trick', 'trick_id', 'trick_id');
    }

    /**
     * Get comments that comment on the video
     */
    public function comments()
    {
        return $this->hasMany('App\Comment', 'video_id', 'video_id');
    }

}
