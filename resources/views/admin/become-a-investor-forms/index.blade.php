@extends('admin.layout.app')

@section('title', __('Become A Investor Forms'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Become A Investor Forms') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Become A Investor Forms') }}</li>
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
                            <h5>{{ __('Become A Investor Forms') }}</h5>
                            <div class="d-flex gap-2">
                                <button type="button" id="deleteSelectedBtn" class="btn btn-danger" style="display: none;">
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                            </div>
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

                        <!-- Search and Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.become-a-investor-forms.index') }}" class="d-flex gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name, email, or mobile...') }}" value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('admin.become-a-investor-forms.index') }}">
                                    @if(request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>{{ __('Unread') }}</option>
                                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>{{ __('Read') }}</option>
                                    </select>
                                </form>
                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Mobile') }}</th>
                                        <th>{{ __('File') }}</th>
                                        <th width="100">{{ __('Status') }}</th>
                                        <th width="120">{{ __('Date') }}</th>
                                        <th width="150">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($forms as $form)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input form-checkbox" value="{{ $form->id }}">
                                            </td>
                                            <td>{{ $form->name }}</td>
                                            <td>{{ $form->email }}</td>
                                            <td>{{ $form->mobile }}</td>
                                            <td>
                                                @if($form->file)
                                                    <a href="{{ $form->file_url }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="material-icons-outlined">download</i>
                                                        {{ $form->file_name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">{{ __('No file') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($form->is_read)
                                                    <span class="badge bg-success">{{ __('Read') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('Unread') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $form->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.become-a-investor-forms.show', $form) }}" class="btn btn-sm btn-primary">
                                                        <i class="material-icons-outlined">visibility</i>
                                                    </a>
                                                    @if(!$form->is_read)
                                                        <button type="button" class="btn btn-sm btn-success mark-as-read-btn" data-id="{{ $form->id }}">
                                                            <i class="material-icons-outlined">mark_email_read</i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-title="{{ $form->name }}" data-url="{{ route('admin.become-a-investor-forms.destroy', $form) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                                                    <p class="mt-2">{{ __('No forms found') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($forms->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $forms->appends(request()->query())->links() }}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const formCheckboxes = document.querySelectorAll('.form-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

    // Select All functionality
    selectAllCheckbox.addEventListener('change', function() {
        formCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteSelectedButton();
    });

    formCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteSelectedButton();
            updateSelectAll();
        });
    });

    function updateDeleteSelectedButton() {
        const checkedBoxes = document.querySelectorAll('.form-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deleteSelectedBtn.style.display = 'inline-flex';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }

    function updateSelectAll() {
        const checkedBoxes = document.querySelectorAll('.form-checkbox:checked');
        const totalBoxes = formCheckboxes.length;
        selectAllCheckbox.checked = checkedBoxes.length === totalBoxes;
        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes;
    }

    // Delete Selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.form-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: `{{ __("Do you want to delete selected form(s)?") }}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("Yes, delete them!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.become-a-investor-forms.delete-selected") }}';
                form.innerHTML = `
                    @csrf
                    ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Mark as Read functionality
    document.querySelectorAll('.mark-as-read-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            fetch(`{{ url('admin/become-a-investor-forms') }}/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');
            
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: `{{ __("Do you want to delete this form?") }}`,
                icon: 'warning',
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