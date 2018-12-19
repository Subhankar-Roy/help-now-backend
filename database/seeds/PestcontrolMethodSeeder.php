<?php
use App\PestControllMethod;
use Illuminate\Database\Seeder;

class PestcontrolMethodSeeder extends Seeder {

    public function run()
    {
        PestControllMethod::insert([
   			[
        		'method_name' => 'Mechanical',
    		],
    		[
        		'method_name' => 'Physical',
    		],
            [
                'method_name' => 'Cultural',
            ],
            [
                'method_name' => 'Chemical',
            ],
            [
                'method_name' => 'Biological',
            ],
            [
                'method_name' => 'Quarantine',
            ],
            [
                'method_name' => 'Other',
            ]
		]);
    }

}
?>
