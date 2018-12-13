<?php
use App\Ethnicity;
use Illuminate\Database\Seeder;

class EthnicityTableSeeder extends Seeder {

    public function run()
    {
        Ethnicity::insert([
   			[
        		'ethnicity' => 'American',
    		],
    		[
        		'ethnicity' => 'Non-American',
    		]
		]);
    }

}
?>
