<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';

    protected $primaryKey = 'tag_id';

    protected $fillable = ['tag_name', 'name_en', 'name_de', 'name_pl', 'name_es', 'name_fr'];

    public $timestamps = false;
}
