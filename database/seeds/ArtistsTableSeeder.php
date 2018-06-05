<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ArtistsTableSeeder extends Seeder
{
	protected $Δ = 100;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $faker = Faker::create('fr_FR');

	    for ($i=0;$i<$this->Δ;$i++) {
		    DB::table('artists')->insert([
			    'unique_name' => $faker->unique()->userName,
			    'name' => $faker->domainWord . ' ' . $faker->emoji,
			    'realname' => $faker->name,
			    'profile' => $faker->realText()
		    ]);
	    }
    }
}
