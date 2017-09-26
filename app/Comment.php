<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $primaryKey = 'comment_id';

    protected $fillable = ['commentator_id', 'video_id', 'status', 'message', 'likes', 'replies'];

    /**
     * Get Video of comment
     */
    public function video()
    {
        return $this->belongsTo('App\Video', 'video_id', 'video_id');
    }

    /**
     * Get commentator
     */
    public function commentator()
    {
        return $this->belongsTo(User::class, 'commentator_id', 'id');
    }
}
