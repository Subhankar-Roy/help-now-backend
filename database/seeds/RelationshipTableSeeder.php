<?php
use App\Relationship;
use Illuminate\Database\Seeder;

class RelationshipTableSeeder extends Seeder {

    public function run()
    {
        Relationship::insert([
   			[
        		'relationship' => 'Unmarried',
    		],
    		[
        		'relationship' => 'Married',
    		],
            [
                'relationship' => 'Divorced',
            ],
            [
                'relationship' => 'Widow',
            ]
		]);
    }

}
?>
