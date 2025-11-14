@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Popup Ads</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Popup Ads</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">check_circle</i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ERROR --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">error</i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Popup Ads List</h5>
                            <a href="{{ route('admin.popup-ads.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body">

                        @if($ads->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th width="100">Image</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th width="200">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-list">

                                        @foreach($ads as $ad)
                                            @php
                                                $mainTranslate = $ad->translation();
                                            @endphp

                                            <tr data-id="{{ $ad->id }}" class="sortable-item">

                                                {{-- Drag Handle --}}
                                                <td>
                                                    <div class="drag-handle">
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                             stroke="currentColor" stroke-width="2">
                                                            <circle cx="8" cy="8" r="1"></circle>
                                                            <circle cx="16" cy="8" r="1"></circle>
                                                            <circle cx="8" cy="16" r="1"></circle>
                                                            <circle cx="16" cy="16" r="1"></circle>
                                                        </svg>
                                                    </div>
                                                </td>

                                                {{-- Image --}}
                                                <td>
                                                    @if($ad->image)
                                                        <img src="{{ asset('storage/' . $ad->image) }}"
                                                             class="img-thumbnail"
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                                             style="width: 60px; height: 60px;">
                                                            <i class="material-icons-outlined text-muted">image</i>
                                                        </div>
                                                    @endif
                                                </td>

                                                {{-- Title --}}
                                                <td>
                                                    <strong>{{ $mainTranslate?->title ?? 'No title' }}</strong>
                                                    @if($mainTranslate)
                                                        <span class="badge bg-info ms-1">
                                                            {{ $mainTranslate->locale }}
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- Status --}}
                                                <td>
                                                    @if($ad->status)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>

                                                {{-- Actions --}}
                                                <td>
                                                    <div class="btn-group" role="group">

                                                        <a href="{{ route('admin.popup-ads.edit', $ad->id) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </a>

                                                      <form action="{{ route('admin.popup-ads.destroy', $ad->id) }}"
                                                                method="POST"
                                                                style="display:inline-block;"
                                                                onsubmit="return confirm('Are you sure you want to delete this item?');">

                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="material-icons-outlined">delete</i>
                                                                </button>

                                                            </form>



                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>

                        @else
                            <div class="text-center py-5">
                                <i class="material-icons-outlined text-muted" style="font-size: 4rem;">image</i>
                                <h5 class="mt-3 text-muted">No popup ads found</h5>
                                <p class="text-muted">Click the button below to add the first popup ad.</p>
                                <a href="{{ route('admin.popup-ads.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // SORTABLE
    const sortableList = document.getElementById('sortable-list');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                const items = Array.from(document.querySelectorAll('.sortable-item'))
                    .map(item => item.dataset.id);

                fetch('{{ route("admin.popup-ads.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").content
                    },
                    body: JSON.stringify({ items })
                });
            }
        });
    }




});
</script>

<style>
.drag-handle {
    cursor: move;
    color: #6c757d;
}
.drag-handle:hover {
    color: #495057;
}
</style>
@endpush
