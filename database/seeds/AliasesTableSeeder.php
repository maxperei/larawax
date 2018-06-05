<?php

use App\Artist;
use Illuminate\Database\Seeder;

class AliasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $dbseeder = new DatabaseSeeder; $Δ = $dbseeder->Δ;
	    $artists = []; $a = Artist::count();

	    foreach (Artist::get(['unique_name', 'name']) as $i => $artist) {
		    $artists['unique_name'][$i] = $artist->unique_name;
		    $artists['name'][$i] = $artist->name;
	    };

	    for ($i=$a-$Δ;$i<$a;$i++) {
		    DB::table('aliases')->insert([
			    'artist_unique_name' => $artists['unique_name'][$i],
			    'name' => $artists['name'][$i]
		    ]);
	    }

	    // TODO Randomize aliases for a same artist
    }
}
