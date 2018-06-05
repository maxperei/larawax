<?php

use App\Artist;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	public $Δ = 10;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$this->call(ArtistsTableSeeder::class);
    	$this->call(AliasesTableSeeder::class);
    }
}
