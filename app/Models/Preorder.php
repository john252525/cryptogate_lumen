<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preorder extends Model
{
    protected $table = 'preorders';

    public $timestamps = false;

    public function user()
    {
        return User::find($this->user_id);
    }
}
