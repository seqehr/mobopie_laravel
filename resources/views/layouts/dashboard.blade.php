@extends('layouts.dashboard')
@section('content')


    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Image</th>
            <th scope="col">caption</th>
            <th scope="col">Location</th>
        </tr>
        </thead>
        <tbody>
        <tr>

            @foreach($posts as $post)

                <td>
                <?php
                echo App\Models\User::where('id', $post->user_id)->get()->first()->name;
                ?>
                <td>


                    @foreach (json_decode($post->inputs) as $input)
                        <img src="https://seeuland.com/webapp/public/sv/{{$input}}"
                             alt="" border=3 height=200 width=200></img>
                    @endforeach
                </td>
                </td>
                <td>{{$post->caption}}</td>
                <td>{{$post->loc}}</td>


        </tr>
    @endforeach

@endsection
