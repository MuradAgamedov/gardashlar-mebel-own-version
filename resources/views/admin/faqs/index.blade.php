@extends('admin.layout.app')

@section('title', 'FAQs')

@section('content')
<!-- Debug CSRF Token -->
<div style="display: none;">
    CSRF Token: {{ csrf_token() }}
</div>
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>FAQs</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#stroke-home') }}"></use>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">FAQs</li>
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
                            <h5>FAQs List</h5>
                            <div class="d-flex gap-2">
                                <form class="d-flex gap-2" method="GET" action="{{ route('admin.faqs.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search questions..." value="{{ request('search') }}" style="width: 300px;">
                                    <button type="submit" class="btn btn-outline-primary" title="Search" style="width: 50px; height: 50px;">
                                        <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">search</i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary" title="Clear" style="width: 50px; height: 50px;">
                                            <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">clear</i>
                                        </a>
                                    @endif
                                </form>
                                <button type="button" id="deleteSelectedBtn" class="btn btn-danger" title="Delete Selected" style="width: 50px; height: 50px; display: none;">
                                    <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">delete</i>
                                </button>
                                <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary" title="Add New FAQ" style="width: 50px; height: 50px;">
                                    <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">add</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($faqs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="display: none;">Order</th>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Question</th>
                                            <th>Status</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-faqs">
                                        @foreach($faqs as $faq)
                                            @php
                                                $mainTranslation = $faq->translates->where('language.is_default', true)->first() 
                                                    ?? $faq->translates->first();
                                            @endphp
                                            <tr data-id="{{ $faq->id }}">
                                                <td class="drag-handle" style="cursor: move; display: none;">
                                                    <div class="d-flex align-items-center">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8 6C8 7.10457 7.10457 8 6 8C4.89543 8 4 7.10457 4 6C4 4.89543 4.89543 4 6 4C7.10457 4 8 4.89543 8 6Z" fill="#6c757d"/>
                                                            <path d="M8 12C8 13.1046 7.10457 14 6 14C4.89543 14 4 13.1046 4 12C4 10.8954 4.89543 10 6 10C7.10457 10 8 10.8954 8 12Z" fill="#6c757d"/>
                                                            <path d="M8 18C8 19.1046 7.10457 20 6 20C4.89543 20 4 19.1046 4 18C4 16.8954 4.89543 16 6 16C7.10457 16 8 16.8954 8 18Z" fill="#6c757d"/>
                                                            <path d="M20 6C20 7.10457 19.1046 8 18 8C16.8954 8 16 7.10457 16 6C16 4.89543 16.8954 4 18 4C19.1046 4 20 4.89543 20 6Z" fill="#6c757d"/>
                                                            <path d="M20 12C20 13.1046 19.1046 14 18 14C16.8954 14 16 13.1046 16 12C16 10.8954 16.8954 10 18 10C19.1046 10 20 10.8954 20 12Z" fill="#6c757d"/>
                                                            <path d="M20 18C20 19.1046 19.1046 20 18 20C16.8954 20 16 19.1046 16 18C16 16.8954 16.8954 16 18 16C19.1046 16 20 16.8954 20 18Z" fill="#6c757d"/>
                                                        </svg>
                                                        <span class="ms-1">{{ $faq->order }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="form-check-input faq-checkbox" value="{{ $faq->id }}">
                                                </td>
                                                <td>
                                                    {{ $mainTranslation->question ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @if($faq->status)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('admin.faqs.edit', $faq) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger delete-faq-btn" 
                                                                data-faq-id="{{ $faq->id }}"
                                                                data-faq-question="{{ $mainTranslation->question ?? 'N/A' }}"
                                                                data-delete-url="{{ route('admin.faqs.destroy', $faq) }}">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="material-icons-outlined" style="font-size: 64px; opacity: 0.3;">help</i>
                                <h5 class="mt-3">No FAQs found</h5>
                                <p>Start by adding your first FAQ.</p>
                                <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary" title="Add First FAQ" style="width: 50px; height: 50px;">
                                    <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">add</i>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if CSRF token is available
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfTokenMeta) {
        console.error('CSRF token meta tag not found!');
        return;
    }
    
    const csrfToken = csrfTokenMeta.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token content is empty!');
        return;
    }
    // Initialize Sortable for FAQs table
    const faqsTbody = document.getElementById('sortable-faqs');
    if (faqsTbody) {
        new Sortable(faqsTbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const rows = faqsTbody.querySelectorAll('tr[data-id]');
                const orderData = [];
                
                rows.forEach((row, index) => {
                    const faqId = row.getAttribute('data-id');
                    orderData.push({
                        id: faqId,
                        order: index + 1
                    });
                    
                    // Update order number in the table
                    const orderCell = row.querySelector('.drag-handle span');
                    if (orderCell) {
                        orderCell.textContent = index + 1;
                    }
                });
                
                // Send reorder request
                fetch('{{ route("admin.faqs.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ faqs: orderData })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Reorder failed:', data.message);
                        alert('Error reordering FAQs: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error reordering FAQs. Please try again.');
                });
            }
        });
    }

    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const faqCheckboxes = document.querySelectorAll('.faq-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

    selectAllCheckbox.addEventListener('change', function() {
        faqCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButtonVisibility();
    });

    faqCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButtonVisibility();
            
            // Update select all checkbox
            const checkedBoxes = document.querySelectorAll('.faq-checkbox:checked');
            const totalBoxes = document.querySelectorAll('.faq-checkbox');
            selectAllCheckbox.checked = checkedBoxes.length === totalBoxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes.length;
        });
    });

    function updateDeleteButtonVisibility() {
        const checkedBoxes = document.querySelectorAll('.faq-checkbox:checked');
        deleteSelectedBtn.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
    }

    // Delete Selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.faq-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            alert('Please select at least one FAQ to delete.');
            return;
        }
        
        if (confirm(`Are you sure you want to delete ${selectedIds.length} selected FAQ(s)?`)) {
            console.log('CSRF Token:', csrfToken);
            console.log('Selected IDs:', selectedIds);
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.faqs.delete-selected.post") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = csrfToken;
            
            const idsField = document.createElement('input');
            idsField.type = 'hidden';
            idsField.name = 'ids';
            idsField.value = JSON.stringify(selectedIds);
            
            form.appendChild(csrfToken);
            form.appendChild(idsField);
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Delete FAQ functionality
    const deleteButtons = document.querySelectorAll('.delete-faq-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const faqId = this.getAttribute('data-faq-id');
            const faqQuestion = this.getAttribute('data-faq-question');
            const deleteUrl = this.getAttribute('data-delete-url');
            
            if (confirm(`Are you sure you want to delete the FAQ: "${faqQuestion}"?`)) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = csrfToken;
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>

<style>
.drag-handle {
    cursor: move;
}

.drag-handle:hover {
    background-color: #f8f9fa;
}

.sortable-ghost {
    opacity: 0.5;
    background-color: #e9ecef;
}

.sortable-chosen {
    background-color: #fff3cd;
}
</style>
@endpush 