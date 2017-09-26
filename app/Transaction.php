<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	protected $table = 'transactions';

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'category_id', 'user_id', 'ref_id', 'value',
    ];

    public function user()
    {
        $this->belongsTo(User::class, 'user_id', 'id');
    }
}
