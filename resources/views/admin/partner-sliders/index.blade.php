@extends('admin.layout.app')

@section('title', __('Partner Sliders'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Partner Sliders') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Partner Sliders') }}</li>
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
                            <h5>{{ __('Partner Sliders') }}</h5>
                            <a href="{{ route('admin.partner-sliders.create') }}" class="btn btn-primary">
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
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th width="100">{{ __('Image') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Link') }}</th>
                                        <th width="80">{{ __('Status') }}</th>
                                        <th width="150">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-sliders">
                                    @forelse($sliders as $slider)
                                        @php
                                            $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                            $firstTranslation = $slider->translates->firstWhere('language_id', $mainLanguageId)
                                                ?? $slider->translates->first();
                                        @endphp
                                        <tr data-id="{{ $slider->id }}">
                                            <td class="drag-handle text-center">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="8" cy="8" r="1"></circle>
                                                    <circle cx="16" cy="8" r="1"></circle>
                                                    <circle cx="8" cy="16" r="1"></circle>
                                                    <circle cx="16" cy="16" r="1"></circle>
                                                </svg>
                                            </td>
                                            <td>
                                                @if($slider->image_url)
                                                    <img src="{{ $slider->image_url }}" alt="{{ __('Slider Image') }}" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 60px;">
                                                        <i class="material-icons-outlined text-muted">image</i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $firstTranslation ? $firstTranslation->title : __('No translation') }}
                                            </td>
                                            <td>
                                                @if($slider->link)
                                                    <a href="{{ $slider->link }}" target="_blank" class="text-primary text-decoration-underline">
                                                        {{ Str::limit($slider->link, 30) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($slider->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.partner-sliders.edit', $slider) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : __('Slider :id', ['id' => $slider->id]) }}"
                                                            data-url="{{ route('admin.partner-sliders.destroy', $slider) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">image</i>
                                                    <p class="mt-2">{{ __('No partner sliders found') }}</p>
                                                    <a href="{{ route('admin.partner-sliders.create') }}" class="btn btn-primary">
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
    // Drag and drop reordering for sliders
    const sortableList = document.getElementById('sortable-sliders');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const sliders = Array.from(sortableList.children).map((row, index) => {
                    return row.getAttribute('data-id');
                });

                fetch('{{ route("admin.partner-sliders.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sliders: sliders })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire({
                            title: '{{ __('Error!') }}',
                            text: data.message || '{{ __('Failed to reorder partner sliders') }}',
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

    // Delete confirmation for sliders
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');

            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('Do you want to delete :title?', ['title' => ':title']) }}'.replace(':title', title),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __('Yes, delete it!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
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
