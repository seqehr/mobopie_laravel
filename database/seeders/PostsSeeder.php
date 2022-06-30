<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Posts;
use App\Models\Tags;
use App\Models\PostTags;
use Faker\Generator as Faker;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            'test tag #1',
            'test tag #2',
            'test tag #3',
            'test tag #4',
        ];
        $inputs = [
            'posts/kZeHtKOnHkXH0Qx3P9N6vwtaSh2cfcN1pKPPBcfT.jpg',
            'posts/kZeHtKOnHkXH0Qx3P9N6vwtaSh2cfcN1pKPPBcfT.jpg',
            'posts/kZeHtKOnHkXH0Qx3P9N6vwtaSh2cfcN1pKPPBcfT.jpg',
        ];
        $inputs =  json_encode($inputs);
        for ($i = 0; $i < 6; $i++) {


            $postdata =   Posts::create([
                'user_id' => 1,
                'caption' =>  "this is a caption #" . $i++,
                'inputs' => $inputs,
                'lat' => '1234.2353',
                'lon' => '124453.435',
                'loc' => 'tehran',
            ]);
            foreach ($tags as $tag) {
                $tagid = Tags::Create([
                    'title' => $tag,
                ]);
                $post_tags = PostTags::create([
                    'post_id' => $postdata->id,
                    'tag_id' => $tagid->id,
                ]);
            }
        }
    }
}
