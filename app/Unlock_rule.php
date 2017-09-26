<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unlock_rule extends Model
{
    protected $table = 'unlock_rule';

    protected $primaryKey = 'id';

    protected $fillable = ['category_id', 'facebook_connect', 'google_connect', 'upload_video_count_per_trick', 'view_count_per_video', 'try_count_amature', 'dribble_average_amature', 'try_count_advanced', 'dribble_average_advanced', 'dribble_score', 'gole_medal_count', 'follower_count', 'following_count'];


   
}
