<?php

use App\Artist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

	    $Δ = 10;
	    $faker = Faker::create('fr_FR');

	    for ($i=0;$i<$Δ;$i++) {
		    DB::table('artists')->insert([
			    'unique_name' => $faker->userName,
			    'name' => $faker->domainWord . ' ' . $faker->emoji,
			    'realname' => $faker->name,
			    'profile' => $faker->realText()
		    ]);
	    }

	    $artists = [];
	    $a = Artist::count();

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
