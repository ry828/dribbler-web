<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dribbler extends Model
{
    protected $table = 'dribblers';

    protected $primaryKey = 'dribbler_id';

    protected $fillable = ['user_id', 'trick_id', 'try_on'];
    
}
