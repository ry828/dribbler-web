<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trick_tag extends Model
{
    protected $table = 'trick_tag';

    protected $primaryKey = 'id';

    protected $fillable = ['trick_id', 'tag_id'];

    public $timestamps = false;
}
