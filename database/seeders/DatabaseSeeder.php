<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\IntrestedCats;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // $user =  User::create([
        //     'name' => 'Sepehr',
        //     'status' => 1,
        //     'img' => '/sv/img/def.png',
        //     'email' => 'seqehr@gmail.com',
        //     'password' => Hash::make('123456789'),
        //     'fcm_token' => 'fHHj6RUAopyI_jDwOzTNnU:APA91bFJ94qIk9DR8BTWQWjJL6p7KPW_leFkONpqcBm6klBLeit0kUa8xMAxdaYcee3h5kLzg-ae1s5VLKqWmbsc1GBp4UFgedjn7hR6w1uvJDy2-wFpcIKk9i6jy-nsODn5xxKiqeec',
        // ]);
        $this->call([
            BusinessCatsSeeder::class,
        ]);
        // $data = [
        //     'food',
        //     'music',
        //     'movies',
        //     'fashion',
        //     'sports',
        //     'game',
        //     'travel',
        //     'calture',
        //     'hobbies',
        //     'work',
        //     'other',
        //     'animals'
        // ];

        // foreach ($data as $data) {

        //     IntrestedCats::create([
        //         'name' => $data
        //     ]);
        // }
    }
}
