@extends('panel::layouts.app')
@section('body-class', 'page-consultations')

@section('title', __('panel::menu.consultations'))

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    <x-panel-data-search
      :action="panel_route('consultations.index')"
      :searchFields="$searchFields ?? []"
      :filters="$filterButtons ?? []"
    />

    @if ($consultations->count())
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <td>{{ __('panel::common.id') }}</td>
            <td>{{ __('panel::consultations.contact_name') }}</td>
            <td>{{ __('panel::common.email') }}</td>
            <td>{{ __('panel::consultations.company') }}</td>
            <td>{{ __('panel::common.status') }}</td>
            <td>{{ __('panel::common.created_at') }}</td>
            <td>{{ __('panel::common.actions') }}</td>
          </tr>
        </thead>
        <tbody>
        @foreach($consultations as $item)
          <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->name ?: '-' }}</td>
            <td>{{ $item->email }}</td>
            <td>{{ $item->company ?: '-' }}</td>
            <td>
              @if($item->status)
                <span class="badge bg-success">{{ __('panel::consultations.read') }}</span>
              @else
                <span class="badge bg-warning text-dark">{{ __('panel::consultations.unread') }}</span>
              @endif
            </td>
            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
            <td>
              <a href="{{ panel_route('consultations.show', [$item->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('panel::common.view') }}</a>
              <form action="{{ panel_route('consultations.destroy', [$item->id]) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('panel::common.delete') }}</button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    {{ $consultations->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
    @else
      <x-common-no-data :width="200" />
    @endif
  </div>
</div>
@endsection
