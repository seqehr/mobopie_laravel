@extends('layouts.dashlayout')
@section('content')



    <table style="background-color: aqua" class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">Name</th>
            <th scope="col">loc</th>
            <th scope="col">caption</th>
            <th scope="col">Image</th>
        </tr>
        </thead>
        <tbody style="background-color: bisque">
        <tr>

            @foreach($posts as $post)

                <td>
                    {{$user->name}}
                </td>

                <td>
                    {{$post->loc}}
                </td>

                <td>
                    {{$post->caption}}
                </td>

                <td>
                    @foreach (json_decode($post->inputs) as $input)
                        <img src="https://seeuland.com/webapp/public/sv/{{$input}}"
                             alt="" border=3 height=200 width=200></img>
                    @endforeach
                </td>


        </tr>
    @endforeach



@endsection
