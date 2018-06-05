<?php

use Illuminate\Database\Seeder;
use App\Artist;
use Faker\Factory as Faker;


class AliasesTableSeeder extends Seeder
{
	protected $Δ = 100;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $artists = [];
	    $a = Artist::count();
	    $faker = Faker::create('fr_FR');

	    foreach (Artist::get(['unique_name']) as $i => $artist) {
		    $artists['unique_name'][$i] = $artist->unique_name;
	    };

	    for ($i=$a-$this->Δ;$i<$a;$i++) {
		    DB::table('aliases')->insert([
			    'artist_unique_name' => $faker->randomElement($artists['unique_name']),
			    'name' => $artists['unique_name'][$i]
		    ]);
	    }

    }
}
