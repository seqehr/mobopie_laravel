@extends('layouts.dashlayout')
@section('content')

    <div style="padding: 10px" class="col-md-3">
        <button type="button" class="btn btn-primary">Add User
        </button>
    </div>

    <table class="table">
        <thead style="background-color: bisque" class="thead-dark">
        <tr>
            <th scope="col">Email</th>
            <th scope="col">Image</th>
            <th scope="col">level</th>
            <th scope="col">Name</th>
        </tr>
        </thead>
        <tbody>
        <tr>

            @foreach($users as $user)

                <td>{{$user->email}}
                <td><img src="https://seeuland.com/webapp/public{{$user->img}}"
                alt="" border=3 height=200 width=200></img>

                <td>{{$user->level }}
                <td>{{$user->name}}<button style="margin: 50px" class="btn btn-warning">
                        Edit
                    </button></td></td>
                </td>

        </tr>
    @endforeach



@endsection
