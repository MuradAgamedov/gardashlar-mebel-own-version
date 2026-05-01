@extends('admin.layout.app')

@section('title', __('KIV Categories'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('KIV Categories') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('KIV Categories') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ __('KIV Categories') }}</h5>
                            <a href="{{ route('admin.kiv-categories.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">{{ __('Order') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('KIV Count') }}</th>
                                        <th width="80">{{ __('Status') }}</th>
                                        <th width="150">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-categories">
                                    @forelse($categories as $category)
                                        @php
                                            $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                            $firstTranslation = $category->translates->firstWhere('language_id', $mainLanguageId)
                                                ?? $category->translates->first();
                                        @endphp
                                        <tr data-id="{{ $category->id }}">
                                            <td>
                                                <div class="drag-handle">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="8" cy="8" r="1"></circle>
                                                        <circle cx="16" cy="8" r="1"></circle>
                                                        <circle cx="8" cy="16" r="1"></circle>
                                                        <circle cx="16" cy="16" r="1"></circle>
                                                    </svg>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $firstTranslation ? $firstTranslation->title : __('No translation') }}</strong>
                                                    @if($firstTranslation && $firstTranslation->subtitle)
                                                        <br><small class="text-muted">{{ $firstTranslation->subtitle }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                       
                                            <td>
                                                <span class="badge bg-info">{{ $category->kivs->count() }}</span>
                                            </td>
                                            <td>
                                                @if($category->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.kiv-categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : __('Category') . ' #' . $category->id }}"
                                                            data-url="{{ route('admin.kiv-categories.destroy', $category) }}"
                                                            data-kiv-count="{{ $category->kivs->count() }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">folder</i>
                                                    <p class="mt-2">{{ __('No KIV categories found') }}</p>
                                                    <a href="{{ route('admin.kiv-categories.create') }}" class="btn btn-primary">
                                                        <i class="material-icons-outlined">add</i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.drag-handle {
    cursor: grab;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
}

.drag-handle:active {
    cursor: grabbing;
}

.sortable-ghost {
    opacity: 0.5;
    background-color: #f8f9fa;
}

.sortable-chosen {
    background-color: #e3f2fd;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Drag and drop reordering for categories
    const sortableList = document.getElementById('sortable-categories');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const categories = Array.from(sortableList.children).map((row, index) => {
                    return row.getAttribute('data-id');
                });

                fetch('{{ route("admin.kiv-categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ categories: categories })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '{{ __("Success!") }}',
                            text: '{{ __("KIV categories reordered successfully!") }}',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: '{{ __("Error!") }}',
                            text: data.message || '{{ __("Failed to reorder KIV categories") }}',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }

    // Delete confirmation for categories
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');
            const kivCount = parseInt(this.getAttribute('data-kiv-count'));

            let message = '{{ __("Do you want to delete \":title\"?", ["title" => ":title"]) }}'.replace(':title', title);
            if (kivCount > 0) {
                message += '\n\n' + '{{ __("This category has :count KIV(s). You must delete all KIVs first.", ["count" => ":count"]) }}'.replace(':count', kivCount);
            }

            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: message,
                icon: kivCount > 0 ? 'warning' : 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("Yes, delete it!") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush 