<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'gender', 'birthday', 'email', 'password', 'verified', 'role', 'subscribe', 'facebook_id', 'google_id',
        'photo', 'confirmation_code', 'status', 'push_enable', 'high_video_enable',
        'following_count', 'follower_count', 'video_count', 'overall_ranking', 'dribble_score', 'trick_completion_count', 'dribbler_medal',
    ];

    /**
     * Setter and Getter
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the tricks that user tries
     */    
    public function tricks()
    {
        return $this->belongsToMany('App\Trick', 'dribblers', 'user_id', 'trick_id')->withPivot('try_on');
    }

    public function trick($trick_id)
    {
        return $this->belongsToMany('App\Trick', 'dribblers', 'user_id', 'trick_id')->withPivot('try_on')->wherePivot('trick_id', $trick_id);   
    }

    /**
     * Get the videos that user owns
     */    
    public function videos()
    {
        return $this->hasMany('App\Video', 'user_id', 'id');
    }

    /**
     * Get following users
     */
    public function followingUsers()
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'user_id');
    }

    /**
     * Get comments
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'commentator_id', 'id');
    }

    /**
     * Get Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }
}
