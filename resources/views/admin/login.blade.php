@extends('layouts.logreg')
@section('content')
<div class="container mt-5">
    <div class="row">
        <h1 class="text-center">Login To Dream Ram ok</h1>
        <div class="col-lg-4 offset-lg-4 col-12 offset-0">
            <form action="" method="post">
                @csrf

                <input type="email" class="form-control my-2" name="email" placeholder="Email">
                <input type="password" class="form-control my-2" name="password" placeholder="Password">
                <input type="submit" class="btn btn-info" value="Login">

            </form>
        </div>
    </div>
</div>

@endsection