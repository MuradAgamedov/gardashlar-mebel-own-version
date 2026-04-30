@extends('admin.layout.app')

@section('content')
<style>
/* Languages page heading fixes */
.languages-page .page-title h3 {
    font-size: 1.5rem !important;
    font-weight: 600 !important;
    margin-bottom: 0.5rem !important;
}

.languages-page .card-header h5 {
    font-size: 1.25rem !important;
    font-weight: 600 !important;
    margin-bottom: 0 !important;
}

.languages-page .card-header.card-no-border h3 {
    font-size: 1.25rem !important;
    font-weight: 600 !important;
    margin-bottom: 0.5rem !important;
}

.languages-page .card-header.card-no-border p {
    font-size: 0.875rem !important;
    color: #6c757d !important;
    margin-bottom: 0 !important;
}
</style>
<div class="page-body languages-page">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Languages Management') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Languages') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">check_circle</i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
                            <h5>{{ __('Languages List') }}</h5>
                            <a href="{{ route('admin.languages.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card overflow-hidden">
                            <div class="card-header card-no-border">
                                <h3>{{ __('Languages Table') }}</h3>
                                <p class="desc mb-0 mt-1">{{ __('Manage your application languages. Drag and drop to reorder.') }}</p>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr class="border-bottom-secondary border-top-0">
                                            <th scope="col" style="width: 50px;"></th>
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col">{{ __('Title') }}</th>
                                            <th scope="col">{{ __('Language Code') }}</th>
                                            <th scope="col">{{ __('Language Name') }}</th>
                                            <th scope="col">{{ __('Order') }}</th>
                                            <th scope="col">{{ __('Status') }}</th>
                                            <th scope="col">{{ __('Main Language') }}</th>
                                            <th scope="col">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-languages">
                                        @forelse($languages as $language)
                                        <tr class="border-bottom-{{ $loop->even ? 'info' : 'success' }}" data-language-id="{{ $language->id }}">
                                            <td class="drag-handle" style="cursor: move;">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 6C8 7.10457 7.10457 8 6 8C4.89543 8 4 7.10457 4 6C4 4.89543 4.89543 4 6 4C7.10457 4 8 4.89543 8 6Z" fill="#6c757d"/>
                                                    <path d="M8 12C8 13.1046 7.10457 14 6 14C4.89543 14 4 13.1046 4 12C4 10.8954 4.89543 10 6 10C7.10457 10 8 10.8954 8 12Z" fill="#6c757d"/>
                                                    <path d="M8 18C8 19.1046 7.10457 20 6 20C4.89543 20 4 19.1046 4 18C4 16.8954 4.89543 16 6 16C7.10457 16 8 16.8954 8 18Z" fill="#6c757d"/>
                                                    <path d="M20 6C20 7.10457 19.1046 8 18 8C16.8954 8 16 7.10457 16 6C16 4.89543 16.8954 4 18 4C19.1046 4 20 4.89543 20 6Z" fill="#6c757d"/>
                                                    <path d="M20 12C20 13.1046 19.1046 14 18 14C16.8954 14 16 13.1046 16 12C16 10.8954 16.8954 10 18 10C19.1046 10 20 10.8954 20 12Z" fill="#6c757d"/>
                                                    <path d="M20 18C20 19.1046 19.1046 20 18 20C16.8954 20 16 19.1046 16 18C16 16.8954 16.8954 16 18 16C19.1046 16 20 16.8954 20 18Z" fill="#6c757d"/>
                                                </svg>
                                            </td>
                                            <th scope="row">{{ $language->id }}</th>
                                            <td>{{ $language->title }}</td>
                                            <td><span class="badge badge-light-primary">{{ $language->lang_code }}</span></td>
                                            <td>{{ $language->lang_name }}</td>
                                            <td>{{ $language->order }}</td>
                                            <td>
                                                @if($language->status)
                                                    <span class="badge badge-light-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge badge-light-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($language->is_main_language)
                                                    <span class="badge badge-light-warning">{{ __('Main') }}</span>
                                                @else
                                                    <span class="badge badge-light-secondary">{{ __('Secondary') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.languages.edit', $language) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger delete-language-btn" 
                                                            data-language-id="{{ $language->id }}"
                                                            data-language-name="{{ $language->title }}"
                                                            data-delete-url="{{ route('admin.languages.destroy', $language) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">{{ __('No languages found') }}</td>
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
</div>

<!-- Hidden form for delete -->
<form id="delete-language-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up delete buttons and sortable...');
    
    // Initialize Sortable
    const sortableList = document.getElementById('sortable-languages');
    if (sortableList) {
        new Sortable(sortableList, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                const languageIds = [];
                const rows = sortableList.querySelectorAll('tr[data-language-id]');
                
                rows.forEach((row, index) => {
                    const languageId = row.getAttribute('data-language-id');
                    if (languageId) {
                        languageIds.push({
                            id: languageId,
                            order: index + 1
                        });
                    }
                });
                
                // Send the new order to the server
                updateLanguageOrder(languageIds);
            }
        });
    }
    
    // SweetAlert for delete confirmation
    document.querySelectorAll('.delete-language-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const languageId = this.getAttribute('data-language-id');
            const languageName = this.getAttribute('data-language-name');
            const deleteUrl = this.getAttribute('data-delete-url');
            
            console.log('Delete button clicked for:', languageName);
            
            if (confirm(`Do you want to delete "${languageName}"? This action cannot be undone.`)) {
                submitDeleteForm(deleteUrl);
            }
        });
    });
    
    function submitDeleteForm(deleteUrl) {
        const form = document.getElementById('delete-language-form');
        form.action = deleteUrl;
        form.submit();
    }
    
    function updateLanguageOrder(languageIds) {
        console.log('Updating language order:', languageIds);
        
        fetch('{{ route("admin.languages.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ languages: languageIds })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Update order numbers in the table
                const rows = sortableList.querySelectorAll('tr[data-language-id]');
                rows.forEach((row, index) => {
                    const orderCell = row.querySelector('td:nth-child(6)'); // Order column (6th column after drag handle)
                    if (orderCell) {
                        orderCell.textContent = index + 1;
                    }
                });
            } else {
                console.error('Error reordering languages:', data.message);
                alert('Error reordering languages: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error updating language order:', error);
            console.error('Error details:', error.message);
        });
    }
});
</script>

<style>
.sortable-ghost {
    opacity: 0.5;
    background: #f8f9fa !important;
}

.sortable-chosen {
    background: #e3f2fd !important;
}

.sortable-drag {
    background: #fff !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.drag-handle {
    cursor: move;
    user-select: none;
}

.drag-handle:hover {
    background-color: #f8f9fa;
}
</style>
@endpush 