@extends('admin.layout.app')

@section('title', 'View Service Form')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>View Service Form</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.service-forms.index') }}">Service Forms</a>
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
                            <h5>Service Form Details</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.service-forms.index') }}" class="btn btn-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                    Back to List
                                </a>
                                <button type="button" class="btn btn-danger delete-btn" data-title="{{ $serviceForm->name }}" data-url="{{ route('admin.service-forms.destroy', $serviceForm) }}">
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
                                    <p class="form-control-static">{{ $serviceForm->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p class="form-control-static">
                                        <a href="mailto:{{ $serviceForm->email }}">{{ $serviceForm->email }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Service:</label>
                                    <p class="form-control-static">
                                        @if($serviceForm->service)
                                            @php
                                                $serviceTranslation = $serviceForm->service->translates->first();
                                            @endphp
                                            <span class="badge bg-primary">
                                                {{ $serviceTranslation ? $serviceTranslation->title : 'Service #' . $serviceForm->service->id }}
                                            </span>
                                        @else
                                            <span class="text-muted">No service</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mobile:</label>
                                    <p class="form-control-static">
                                        <a href="tel:{{ $serviceForm->mobile }}">{{ $serviceForm->mobile }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status:</label>
                                    <p class="form-control-static">
                                        @if($serviceForm->is_read)
                                            <span class="badge bg-success">Read</span>
                                            @if($serviceForm->read_at)
                                                <small class="text-muted d-block">Read at: {{ $serviceForm->read_at->format('d.m.Y H:i') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">Unread</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Submitted:</label>
                                    <p class="form-control-static">{{ $serviceForm->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">File:</label>
                                    <p class="form-control-static">
                                        @if($serviceForm->file)
                                            <a href="{{ $serviceForm->file_url }}" target="_blank" class="btn btn-info">
                                                <i class="material-icons-outlined">download</i>
                                                {{ $serviceForm->file_name }}
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
                                    <div style="color: black !important" class="border rounded p-3 bg-light">
                                        {!! nl2br(e($serviceForm->message)) !!}
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