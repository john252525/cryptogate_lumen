<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'task';

    public $timestamps = false;

    public function preorder() {
        return Preorder::find($this->preorder_id);
    }
}
