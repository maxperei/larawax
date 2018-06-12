<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    public function alias()
    {
    	return $this->hasMany(Alias::class, 'artist_unique_name', 'unique_name');
    }
}
