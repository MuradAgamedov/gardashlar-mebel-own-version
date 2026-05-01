@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Individual Project Orders') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Individual Project Orders') }}</li>
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
                            <h5>{{ __('Individual Project Orders List') }}</h5>
                            <div class="d-flex gap-2">
                                <button type="button" id="deleteSelectedBtn" class="btn btn-danger" style="display: none;">
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                                <a href="{{ route('admin.individual-project-orders.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        </div>
                        
                        @if(request('search'))
                            <div class="mt-2">
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="material-icons-outlined">info</i>
                                    <strong>{{ __('Search Results:') }}</strong>
                                    <span class="badge bg-primary me-2">{{ __('Title/Description:') }} "{{ request('search') }}"</span>
                                    <span class="badge bg-secondary">{{ __('count result(s)', ['count' => $individualProjectOrders->count()]) }}</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Search Form -->
                        <div class="mt-3">
                            <form action="{{ route('admin.individual-project-orders.index') }}" method="GET" class="row g-3">
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="material-icons-outlined">search</i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               name="search" 
                                               value="{{ request('search') }}"
                                               placeholder="{{ __('Search by title or description...') }}">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-outline-primary" title="{{ __('Search') }}" style="width: 50px; height: 50px;">
                                        <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">search</i>
                                    </button>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('admin.individual-project-orders.index') }}" class="btn btn-outline-secondary" title="{{ __('Clear') }}" style="width: 50px; height: 50px;">
                                        <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">clear</i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($individualProjectOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th width="50">#</th>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th width="200">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-list">
                                        @foreach($individualProjectOrders as $order)
                                            <tr data-id="{{ $order->id }}" class="sortable-item">
                                                <td>
                                                    <input type="checkbox" class="form-check-input item-checkbox" value="{{ $order->id }}">
                                                </td>
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
                                                        @php
                                                            $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                            $mainTranslate = $order->translates->firstWhere('language_id', $mainLanguageId)
                                                                ?? $order->translates->first();
                                                        @endphp
                                                        <strong>{{ $mainTranslate?->title ?? __('No title') }}</strong>
                                                        @if($mainTranslate)
                                                            <span class="badge bg-info ms-1">{{ $mainTranslate->language->lang_code }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ Str::limit($mainTranslate?->description ?? '', 100) }}</small>
                                                </td>
                                                <td>
                                                    @if($order->status)
                                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td style="display: none;">{{ $order->order }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.individual-project-orders.edit', $order->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger delete-btn" 
                                                                data-id="{{ $order->id }}"
                                                                data-title="{{ $mainTranslate?->title ?? __('This order') }}">
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
                            <div class="text-center py-5">
                                <i class="material-icons-outlined text-muted" style="font-size: 4rem;">info</i>
                                <h5 class="mt-3 text-muted">{{ __('No individual project orders found') }}</h5>
                                <p class="text-muted">{{ __('Click the button below to add the first individual project order.') }}</p>
                                <a href="{{ route('admin.individual-project-orders.create') }}" class="btn btn-primary">
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
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
    });

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButton();
            updateSelectAll();
        });
    });

    function updateDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deleteSelectedBtn.style.display = 'inline-flex';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }

    function updateSelectAll() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const totalBoxes = itemCheckboxes.length;
        selectAllCheckbox.checked = checkedBoxes.length === totalBoxes;
        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes;
    }

    // Delete Selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: `{{ __("Are you sure you want to delete :count selected item(s)?", ["count" => $individualProjectOrders->count()]) }}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.individual-project-orders.delete-selected") }}';
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                    ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Initialize Sortable
    const sortableList = document.getElementById('sortable-list');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.querySelectorAll('.sortable-item')).map(item => item.dataset.id);
                
                fetch('{{ route("admin.individual-project-orders.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ order: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update order numbers in the table
                        const rows = sortableList.querySelectorAll('.sortable-item');
                        rows.forEach((row, index) => {
                            const orderCell = row.querySelector('td:nth-child(5)');
                            if (orderCell) {
                                orderCell.textContent = index + 1;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }

    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: `{{ __("Are you sure you want to delete \":title\"?", ["title" => ""]) }}${title}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("Yes, delete!") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/individual-project-orders/${id}`;
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

<style>
.drag-handle {
    cursor: move;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.drag-handle:hover {
    color: #495057;
}
</style>
@endpush