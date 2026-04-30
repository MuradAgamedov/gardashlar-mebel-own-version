@extends('admin.layout.app')

@section('title', __('View Suggestion or Complaint'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('View Suggestion / Complaint') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.suggestions-complaints-forms.index') }}">{{ __('All Forms') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('View') }}</li>
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
                        <h5>{{ __('Form Details') }}</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.suggestions-complaints-forms.index') }}" class="btn btn-secondary">
                                <i class="material-icons-outlined">arrow_back</i> {{ __('Back') }}
                            </a>
                            <button type="button" 
                                    class="btn btn-danger delete-btn"
                                    data-title="{{ $form->name }}"
                                    data-url="{{ route('admin.suggestions-complaints-forms.destroy', $form->id) }}">
                                <i class="material-icons-outlined">delete</i> {{ __('Delete') }}
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Name:') }}</label>
                                <p class="form-control-plaintext">{{ $form->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Email:') }}</label>
                                <p class="form-control-plaintext">
                                    <a href="mailto:{{ $form->email }}">{{ $form->email }}</a>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Mobile:') }}</label>
                                <p class="form-control-plaintext">
                                    <a href="tel:{{ $form->mobile }}">{{ $form->mobile }}</a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Status:') }}</label>
                                <p class="form-control-plaintext">
                                    @if($form->is_read)
                                        <span class="badge bg-success">{{ __('Read') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('Unread') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">{{ __('Message:') }}</label>
                                <div style="color: black !important" class="">
                                    {!! nl2br(e($form->message)) !!}
                                </div>
                            </div>
                        </div>

                        @if($form->file)
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">{{ __('Attached File:') }}</label>
                                <p>
                                    <a href="{{ asset('storage/' . $form->file) }}" target="_blank" class="btn btn-info">
                                        <i class="material-icons-outlined">download</i> {{ __('Download File') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Submitted At:') }}</label>
                                <p class="form-control-plaintext">{{ $form->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('Last Updated:') }}</label>
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
                title: '{{ __("Are you sure?") }}',
                text: `{{ __("Do you want to delete \":title\"?", ["title" => $form->name]) }}`,
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
