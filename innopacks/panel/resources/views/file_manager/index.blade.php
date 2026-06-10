@extends(request()->header('X-Iframe') ? 'panel::layouts.blank' : 'panel::layouts.app')

@section('title', __('panel/file_manager.title'))

@include('panel::file_manager.main')
