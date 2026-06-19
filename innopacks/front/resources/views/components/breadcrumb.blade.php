@php
  $lastIndex = count($breadcrumbs) - 1;
@endphp

<nav aria-label="breadcrumb" class="aurora-breadcrumb aurora-reveal">
  @foreach ($breadcrumbs as $index => $breadcrumb)
    @if ($index > 0)
      <i class="bi bi-chevron-right aurora-breadcrumb__sep"></i>
    @endif
    @php
      $url = $index === $lastIndex ? null : ($breadcrumb['url'] ?? null);
    @endphp
    @if (! empty($url))
      <a href="{{ $url }}" class="aurora-breadcrumb__link" @if(isset($breadcrumb['full_title'])) title="{{ $breadcrumb['full_title'] }}" @endif>
        @if ($index === 0)
          <i class="bi bi-house-door"></i>
        @endif
        <span>{{ $breadcrumb['display_title'] ?? $breadcrumb['title'] }}</span>
      </a>
    @else
      <span class="aurora-breadcrumb__current" @if(isset($breadcrumb['full_title'])) title="{{ $breadcrumb['full_title'] }}" @endif>
        {{ $breadcrumb['display_title'] ?? $breadcrumb['title'] }}
      </span>
    @endif
  @endforeach
</nav>
