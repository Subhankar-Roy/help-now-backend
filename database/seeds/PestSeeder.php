<?php
use App\PestCatagory;
use Illuminate\Database\Seeder;

class PestSeeder extends Seeder {

    public function run()
    {
        PestCatagory::insert([
   			[
                'id' => 1,
        		'pest_name' => 'Insects',
                'pest_catagory' => 0,
    		],
    		[
        		'id' => 2,
                'pest_name' => 'Mites',
                'pest_catagory' => 0,
    		],
            [
                'id' => 3,
                'pest_name' => 'Rodents',
                'pest_catagory' => 0,
            ],
            [
                'id' => 4,
                'pest_name' => 'Animals',
                'pest_catagory' => 0,
            ],
            [
                'id' => 5,
                'pest_name' => 'Birds',
                'pest_catagory' => 0,
            ],
            [   
                'id' => 6,
                'pest_name' => 'Bedbugs',
                'pest_catagory' => 1,
            ],
            [
                'id' => 7,
                'pest_name' => 'Termites',
                'pest_catagory' => 1,
            ],
            [
                'id' => 8,
                'pest_name' => 'Wasps',
                'pest_catagory' => 1,
            ],
            [
                'id' => 9,
                'pest_name' => 'Red Spider Mite',
                'pest_catagory' => 2,
            ],
            [   
                'id' => 10,
                'pest_name' => 'Panonychus Ulmi',
                'pest_catagory' => 2,
            ],
            [
                'id' => 11,
                'pest_name' => 'Cyclamen Mite',
                'pest_catagory' => 2,
            ],
            [
                'id' => 12,
                'pest_name' => 'Dry Bulb Mite',
                'pest_catagory' => 2,
            ],
            [
                'id' => 13,
                'pest_name' => 'Rats',
                'pest_catagory' => 3,
            ],
            [
                'id' => 14,
                'pest_name' => 'Gropher',
                'pest_catagory' => 3,
            ],
            [
                'id' => 15,
                'pest_name' => 'Racoon',
                'pest_catagory' => 4,
            ],
            [
                'id' => 16,
                'pest_name' => 'Pigeon',
                'pest_catagory' => 5,
            ],
            [
                'id' => 17,
                'pest_name' => 'Crow',
                'pest_catagory' => 5,
            ],
            [
                'id' => 18,
                'pest_name' => 'Sparrow',
                'pest_catagory' => 5,
            ],
            [
                'id' => 19,
                'pest_name' => 'Parrot',
                'pest_catagory' => 5,
            ],
		]);
    }

}
?>
