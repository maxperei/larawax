<?php

use App\Artist;
use Illuminate\Database\Seeder;

class AliasesTableSeeder extends Seeder
{
	protected $Δ = 10;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $artists = [];
	    $a = Artist::count();

	    foreach (Artist::get(['unique_name', 'name']) as $i => $artist) {
		    $artists['unique_name'][$i] = $artist->unique_name;
		    $artists['name'][$i] = $artist->name;
	    };

	    // TODO Randomize aliases for a same artist
	    for ($i=$a-$this->Δ;$i<$a;$i++) {
		    DB::table('aliases')->insert([
			    'artist_unique_name' => $artists['unique_name'][$i],
			    'name' => $artists['name'][$i]
		    ]);
	    }

    }
}
