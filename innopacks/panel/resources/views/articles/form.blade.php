@extends('panel::layouts.app')
@section('body-class', 'page-article-form')
@section('title', __('panel/menu.articles'))

<x-panel::form.right-btns formid="article-form" />

@section('content')
  <form class="needs-validation no-load" novalidate
    action="{{ $article->id ? panel_route('articles.update', [$article->id]) : panel_route('articles.store') }}"
    method="POST" id="article-form">
    @csrf
    @method($article->id ? 'PUT' : 'POST')

    <div class="card">
      <div class="card-body">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-tab-pane"
              type="button" role="tab" aria-controls="basic-tab-pane"
              aria-selected="true">{{ __('panel/common.basic_info') }}</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#content-tab-pane" type="button"
              role="tab" aria-controls="content-tab-pane" aria-selected="false">{{ __('panel/article.article_content') }}</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="extra-tab" data-bs-toggle="tab" data-bs-target="#extra-tab-pane"
              type="button" role="tab" aria-controls="extra-tab-pane"
              aria-selected="false">{{ __('panel/article.extra_info') }}</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo-tab-pane"
              type="button" role="tab" aria-controls="seo-tab-pane"
              aria-selected="false">{{ __('panel/common.seo') }}</button>
          </li>
          @hookinsert('panel.article.edit.tab.nav.bottom')
        </ul>

        <div class="tab-content" id="myTabContent">
          @include('panel::articles.panes.tab_pane_basic')
          @include('panel::articles.panes.tab_pane_content')
          @include('panel::articles.panes.tab_pane_extra')
          @include('panel::articles.panes.tab_pane_seo')

          @hookinsert('panel.article.edit.tab.pane.bottom')
        </div>
      </div>
    </div>

    <button type="submit" class="d-none"></button>
  </form>
@endsection

@push('footer')
<script>
$('#article-form').on('keypress', function(e) {
  if (e.which === 13) e.preventDefault();
});
</script>
@endpush
