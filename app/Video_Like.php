<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video_Like extends Model
{
    protected $table = 'video_likes';

    protected $primaryKey = 'like_id';

    protected $fillable = ['user_id', 'video_id', 'like_type', 'view_count'];

    public $timestamps = null;
}
