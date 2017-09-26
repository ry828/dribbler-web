<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'category_id';

    protected $fillable = ['category_title', 'thumbnail', 'lock', 'price'];


    /**
     * Get Tricks of Category
     */
    public function tricks() 
    {
    	return $this->hasMany(Trick::class, 'category_id', 'category_id');
    }
}
