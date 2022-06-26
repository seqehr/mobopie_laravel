<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessCats;

class BusinessCatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cats = [
            [
                'name' => 'Automotive',
                'children' => [
                    'Auto Accessories',
                    'Auto Dealers – New',
                    'Auto Dealers – Used',
                    'Detail & Carwash',
                    'Gas Stations',

                ]
            ],
            [
                'name' => 'Business Support & Supplies',
                'children' => [
                    'Consultants',
                    'Employment Agency',
                    'Marketing & Communications',
                ]
            ],
            [
                'name' => 'Computers & Electronics',
                'children' => [
                    'Computer Programming & Support',
                    'Consumer Electronics & Accessories',
                    'Marketing & Communications',
                ]
            ],
            [
                'name' => 'Construction & Contractors',
                'children' => [
                    'Architects, Landscape Architects, Engineers & Surveyors',
                    'Blasting & Demolition',
                    'Building Materials & Supplies',
                ]
            ],
        ];

        foreach ($cats as $cat) {
            $parent =  BusinessCats::create([
                'name' => $cat['name'],
            ]);
            foreach ($cat['children'] as $child) {
                BusinessCats::create([
                    'name' => $child,
                    'parent_id' => $parent->id
                ]);
            }
        }
    }
}
