<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stories;

class StoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 6; $i++) {
            $db = Stories::create([
                'user_id' => 1,
                'input' => 'stories/PIIdfe5vSbuTFEbFoFm4padNONZLDOovBQwXBrUy.jpg',
            ]);
        }
    }
}
