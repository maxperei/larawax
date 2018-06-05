<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ArtistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$dbseeder = new DatabaseSeeder; $Δ = $dbseeder->Δ;
	    $faker = Faker::create('fr_FR');

	    for ($i=0;$i<$Δ;$i++) {
		    DB::table('artists')->insert([
			    'unique_name' => $faker->userName,
			    'name' => $faker->domainWord . ' ' . $faker->emoji,
			    'realname' => $faker->name,
			    'profile' => $faker->realText()
		    ]);
	    }
    }
}
