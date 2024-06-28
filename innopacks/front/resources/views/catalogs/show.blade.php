@extends('layouts.app')

@section('body-class', 'page-news')

@section('content')

@include('shared.page-head', ['title' => $catalog->translation->title])
@include('shared.articles')

@endsection

