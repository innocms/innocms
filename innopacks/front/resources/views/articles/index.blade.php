@extends('layouts.app')

@section('body-class', 'page-news')

@section('content')

@include('shared.page-head', ['title' => '新闻资讯'])
@include('shared.articles')

@endsection