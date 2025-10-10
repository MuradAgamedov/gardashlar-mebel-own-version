@extends('admin.layout.app')

@section('title', 'View Suggestion or Complaint')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>View Suggestion / Complaint</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.suggestions-complaints-forms.index') }}">All Forms</a>
                        </li>
                        <li class="breadcrumb-item active">View</li>
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
                        <h5>Form Details</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.suggestions-complaints-forms.index') }}" class="btn btn-secondary">
                                <i class="material-icons-outlined">arrow_back</i> Back
                            </a>
                            <button type="button" 
                                    class="btn btn-danger delete-btn"
                                    data-title="{{ $form->name }}"
                                    data-url="{{ route('admin.suggestions-complaints-forms.destroy', $form->id) }}">
                                <i class="material-icons-outlined">delete</i> Delete
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Name:</label>
                                <p class="form-control-plaintext">{{ $form->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email:</label>
                                <p class="form-control-plaintext">
                                    <a href="mailto:{{ $form->email }}">{{ $form->email }}</a>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mobile:</label>
                                <p class="form-control-plaintext">
                                    <a href="tel:{{ $form->mobile }}">{{ $form->mobile }}</a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status:</label>
                                <p class="form-control-plaintext">
                                    @if($form->is_read)
                                        <span class="badge bg-success">Read</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Unread</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Message:</label>
                                <div class="border rounded p-3 bg-light">
                                    {!! nl2br(e($form->message)) !!}
                                </div>
                            </div>
                        </div>

                        @if($form->file)
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Attached File:</label>
                                <p>
                                    <a href="{{ asset('storage/' . $form->file) }}" target="_blank" class="btn btn-info">
                                        <i class="material-icons-outlined">download</i> Download File
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Submitted At:</label>
                                <p class="form-control-plaintext">{{ $form->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Last Updated:</label>
                                <p class="form-control-plaintext">{{ $form->updated_at->format('d.m.Y H:i') }}</p>
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
