<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    protected $fillable = ['name'];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_tag', 'tag_id', 'client_id');
    }
}
