<?php
use App\Education;
use Illuminate\Database\Seeder;

class EducationTableSeeder extends Seeder {

    public function run()
    {
        Education::insert([
   			[
        		'education' => '10Th',
    		],
    		[
        		'education' => '12Th',
    		],
            [
                'education' => 'Graduate',
            ],
            [
                'education' => 'Post Graduate',
            ]
		]);
    }

}
?>
