@extends('layouts.app')

@section('body-class', 'page-news')

@section('content')

@include('shared.page-head', ['title' => $sidebarCatalog->translation->title ?? __('front::common.news')])
@include('shared.articles')

@endsection