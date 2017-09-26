<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_Category extends Model
{
    protected $table = 'User_Category';

    protected $primaryKey = 'id';

    protected $fillable = ['user_id', 'category_id', 'permission'];

    public $timestamps = false;
}
