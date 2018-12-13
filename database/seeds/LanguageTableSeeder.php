<?php
use App\Language;
use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder {

    public function run()
    {
        Language::insert([
   			[
        		'language' => 'English',
    		],
    		[
        		'language' => 'Spanish',
    		]
		]);
    }

}
?>
