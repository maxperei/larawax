<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alias extends Model
{
    public function artist()
    {
    	return $this->belongsTo(Artist::class, 'artist_unique_name', 'unique_name');
    }
}
