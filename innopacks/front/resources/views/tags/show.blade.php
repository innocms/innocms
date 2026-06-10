@extends('layouts.app')

@section('body-class', 'page-news')

@section('content')

@include('shared.page-head', ['title' => $tag->name])
@include('shared.articles')

@endsection

