<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    protected $fillable = ['first_name', 'last_name', 'second_name', 'phone', 'company_name', 'comment'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'client_tag', 'client_id', 'tag_id');
    }
}
