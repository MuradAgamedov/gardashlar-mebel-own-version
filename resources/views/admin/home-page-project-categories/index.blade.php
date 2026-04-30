@extends('admin.layout.app')

@section('title', __('Home Page Project Categories'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Home Page Project Categories') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i> {{ __('Home') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Home Page Project Categories') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ __('Project Categories to Display on Home Page') }}</h5>
                            <a href="{{ route('admin.home-page-project-categories.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Order') }}</th>
                                        <th width="150">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-categories">
                                    @forelse($homePageProjectCategories as $homePageProjectCategory)
                                        <tr data-id="{{ $homePageProjectCategory->id }}">
                                            <td>
                                                <div class="drag-handle">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="8" cy="8" r="1"></circle>
                                                        <circle cx="16" cy="8" r="1"></circle>
                                                        <circle cx="8" cy="16" r="1"></circle>
                                                        <circle cx="16" cy="16" r="1"></circle>
                                                    </svg>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">category</i>
                                                    <div>
                                                        @php
                                                            $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                            $firstTranslation = $homePageProjectCategory->projectCategory->translates->firstWhere('language_id', $mainLanguageId)
                                                                ?? $homePageProjectCategory->projectCategory->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : __('No title') }}</strong>
                                                        @if($firstTranslation && $firstTranslation->description)
                                                            <br><small class="text-muted">{{ Str::limit($firstTranslation->description, 100) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($homePageProjectCategory->status)
                                                    <span class="badge bg-success">
                                                        <i class="material-icons-outlined" style="font-size: 12px;">check_circle</i>
                                                        {{ __('Active') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="material-icons-outlined" style="font-size: 12px;">cancel</i>
                                                        {{ __('Inactive') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $homePageProjectCategory->order }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.home-page-project-categories.edit', $homePageProjectCategory->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.home-page-project-categories.destroy', $homePageProjectCategory->id) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('{{ __('Are you sure you want to remove this project category from home page?') }}')"
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="material-icons-outlined" style="font-size: 48px; color: #6c757d;">folder_open</i>
                                                <h5 class="text-muted mt-3">{{ __('No Project Categories Selected') }}</h5>
                                                <p class="text-muted">{{ __('Start by adding project categories to display on the home page.') }}</p>
                                                <a href="{{ route('admin.home-page-project-categories.create') }}" class="btn btn-primary">
                                                    <i class="material-icons-outlined">add</i>
                                                </a>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.getElementById('sortable-categories');
    
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.children).map((tr, index) => {
                    return tr.getAttribute('data-id');
                }).filter(id => id !== null);

                // Send reorder request
                fetch('{{ route("admin.home-page-project-categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                            {{ __('Order updated successfully') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
                        
                        // Auto remove after 3 seconds
                        setTimeout(() => {
                            alert.remove();
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }
});
</script>
@endpush
@endsection 