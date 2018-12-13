<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StateTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(EducationTableSeeder::class);
        $this->call(EthnicityTableSeeder::class);
        $this->call(RelationshipTableSeeder::class);
    }
}
