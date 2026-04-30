@extends('admin.layout.app')

@section('title', __('Suggestions & Complaints Forms'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Suggestions & Complaints Forms') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Suggestions & Complaints') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>{{ __('Suggestions & Complaints') }}</h5>
                        <button id="deleteSelectedBtn" class="btn btn-danger" style="display:none;">
                            <i class="material-icons-outlined">delete</i>
                        </button>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="50"><input type="checkbox" id="selectAll"></th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Mobile') }}</th>
                                        <th>{{ __('Message') }}</th>
                                        <th>{{ __('File') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th width="120">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($forms as $form)
                                        <tr @class(['table-warning' => !$form->is_read])>
                                            <td><input type="checkbox" class="form-checkbox" value="{{ $form->id }}"></td>
                                            <td>{{ $form->name }}</td>
                                            <td>{{ $form->email }}</td>
                                            <td>{{ $form->mobile }}</td>
                                            <td>{{ Str::limit($form->message, 50) }}</td>
                                            <td>
                                                @if($form->file)
                                                    <a href="{{ asset('storage/'.$form->file) }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="material-icons-outlined">download</i> {{ __('File') }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">{{ __('No file') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($form->is_read)
                                                    <span class="badge bg-success">{{ __('Read') }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">{{ __('Unread') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $form->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.suggestions-complaints-forms.show', $form->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="material-icons-outlined">visibility</i>
                                                    </a>
                                                    @if(!$form->is_read)
                                                        <button type="button" class="btn btn-sm btn-success mark-as-read" data-id="{{ $form->id }}">
                                                            <i class="material-icons-outlined">mark_email_read</i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $form->id }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                <i class="material-icons-outlined" style="font-size:40px;">inbox</i>
                                                <p class="mt-2 mb-0">{{ __('No forms found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $forms->links() }}
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
document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.form-checkbox');
    const deleteBtn = document.getElementById('deleteSelectedBtn');

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleDeleteButton();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', toggleDeleteButton));

    function toggleDeleteButton() {
        const checked = document.querySelectorAll('.form-checkbox:checked').length;
        deleteBtn.style.display = checked > 0 ? 'inline-flex' : 'none';
    }

    deleteBtn.addEventListener('click', () => {
        const ids = Array.from(document.querySelectorAll('.form-checkbox:checked')).map(cb => cb.value);
        if (!ids.length) return;

        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: `{{ __("Delete selected form(s)?") }}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, delete") }}',
            confirmButtonColor: '#d33'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('{{ route("admin.suggestions-complaints-forms.delete-selected") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ids })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) location.reload();
                });
            }
        });
    });

    document.querySelectorAll('.mark-as-read').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            fetch(`/admin/suggestions-complaints/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            }).then(() => location.reload());
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("This form will be permanently deleted.") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __("Yes, delete it!") }}',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/suggestions-complaints/${id}`;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
