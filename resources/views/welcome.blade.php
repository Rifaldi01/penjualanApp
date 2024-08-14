@extends('layouts.master')
@section('content')
    <h2>Welcome Back, {{Auth::user()->name}}</h2>
@endsection

@push('head') @endpush
@push('js') @endpush
