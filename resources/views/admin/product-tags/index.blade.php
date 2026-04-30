@extends('admin.layout.app')

@section('title', __('Product Tags'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Product Tags') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Product Tags') }}</li>
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
                            <h5>{{ __('Product Tags') }}</h5>
                            <a href="{{ route('admin.product-tags.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                {{ __('Add Tag') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Slug') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Order') }}</th>
                                        <th width="150">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-tags">
                                    @forelse($productTags as $productTag)
                                        <tr data-id="{{ $productTag->id }}">
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
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">local_offer</i>
                                                    <div>
                                                        @php
                                                            $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                            $firstTranslation = $productTag->translates->firstWhere('language_id', $mainLanguageId)
                                                                ?? $productTag->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : __('No title') }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $firstTranslation ? $firstTranslation->slug : __('No slug') }}</code>
                                            </td>
                                            <td>
                                                @if($productTag->status)
                                                    <span class="badge badge-light-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge badge-light-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info">
                                                    <i class="material-icons-outlined" style="font-size: 12px;">sort</i>
                                                    {{ $productTag->order }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.product-tags.edit', $productTag) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="{{ __('Edit') }}">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.product-tags.destroy', $productTag) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this tag?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="{{ __('Delete') }}">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">local_offer</i>
                                                    <p class="mt-2">{{ __('No product tags added yet') }}</p>
                                                    <a href="{{ route('admin.product-tags.create') }}" class="btn btn-primary">
                                                        {{ __('Add First Tag') }}
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.getElementById('sortable-tags');
    
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.children).map((tr, index) => {
                    return tr.getAttribute('data-id');
                }).filter(id => id !== null);

                // Send reorder request
                fetch('{{ route("admin.product-tags.reorder") }}', {
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