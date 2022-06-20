@extends('layouts.dashlayout')
@section('content')
<h4>Edit User</h4>
<form action="" method="post">
    @csrf
    <input type="hidden" name="id" value="{{$user->id}}">
    <input type="text" name="name" class="form-control my-2" placeholder="name" value="{{$user->name}}">
    <input type="text" name="email" class="form-control my-2" placeholder="email" value="{{$user->email}}">

    <input type="submit">
</form>

@endsection