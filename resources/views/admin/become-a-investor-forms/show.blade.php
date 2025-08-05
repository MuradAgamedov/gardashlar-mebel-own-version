@extends('admin.layout.app')

@section('title', 'View Investor Form')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>View Investor Form</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.become-a-investor-forms.index') }}">Become A Investor Forms</a>
                        </li>
                        <li class="breadcrumb-item active">View Form</li>
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
                            <h5>Investor Form Details</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.become-a-investor-forms.index') }}" class="btn btn-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                    Back to List
                                </a>
                                <button type="button" class="btn btn-danger delete-btn" data-title="{{ $becomeAInvestorForm->name }}" data-url="{{ route('admin.become-a-investor-forms.destroy', $becomeAInvestorForm) }}">
                                    <i class="material-icons-outlined">delete</i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Name:</label>
                                    <p class="form-control-static">{{ $becomeAInvestorForm->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p class="form-control-static">
                                        <a href="mailto:{{ $becomeAInvestorForm->email }}">{{ $becomeAInvestorForm->email }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mobile:</label>
                                    <p class="form-control-static">
                                        <a href="tel:{{ $becomeAInvestorForm->mobile }}">{{ $becomeAInvestorForm->mobile }}</a>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status:</label>
                                    <p class="form-control-static">
                                        @if($becomeAInvestorForm->is_read)
                                            <span class="badge bg-success">Read</span>
                                            @if($becomeAInvestorForm->read_at)
                                                <small class="text-muted d-block">Read at: {{ $becomeAInvestorForm->read_at->format('d.m.Y H:i') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">Unread</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Submitted:</label>
                                    <p class="form-control-static">{{ $becomeAInvestorForm->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">File:</label>
                                    <p class="form-control-static">
                                        @if($becomeAInvestorForm->file)
                                            <a href="{{ $becomeAInvestorForm->file_url }}" target="_blank" class="btn btn-info">
                                                <i class="material-icons-outlined">download</i>
                                                {{ $becomeAInvestorForm->file_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">No file uploaded</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Message:</label>
                                    <div class="border rounded p-3 bg-light">
                                        {!! nl2br(e($becomeAInvestorForm->message)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
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
    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${title}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
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