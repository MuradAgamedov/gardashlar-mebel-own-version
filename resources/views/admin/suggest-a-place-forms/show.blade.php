@extends('admin.layout.app')

@section('title', __('View Place Form'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('View Place Form') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.suggest-a-place-forms.index') }}">{{ __('Suggest A Place Forms') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('View Form') }}</li>
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
                            <h5>{{ __('Place Form Details') }}</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.suggest-a-place-forms.index') }}" class="btn btn-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                    {{ __('Back to List') }}
                                </a>
                                <button type="button" class="btn btn-danger delete-btn" data-title="{{ $suggestAPlaceForm->name_surname }}" data-url="{{ route('admin.suggest-a-place-forms.destroy', $suggestAPlaceForm) }}">
                                    <i class="material-icons-outlined">delete</i>
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Name Surname:') }}</label>
                                    <p class="form-control-static">{{ $suggestAPlaceForm->name_surname }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Email:') }}</label>
                                    <p class="form-control-static">
                                        <a href="mailto:{{ $suggestAPlaceForm->email }}">{{ $suggestAPlaceForm->email }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Area kv²:') }}</label>
                                    <p class="form-control-static">{{ $suggestAPlaceForm->area_sqm }} m²</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Mobile:') }}</label>
                                    <p class="form-control-static">
                                        <a href="tel:{{ $suggestAPlaceForm->mobile }}">{{ $suggestAPlaceForm->mobile }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Address:') }}</label>
                                    <p class="form-control-static">{{ $suggestAPlaceForm->address }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Floor count:') }}</label>
                                    <p class="form-control-static">{{ $suggestAPlaceForm->floor_count }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Repair status:') }}</label>
                                    <p class="form-control-static">
                                        <span class="badge {{ $suggestAPlaceForm->is_repaired ? 'bg-success' : 'bg-warning' }}">
                                            {{ $suggestAPlaceForm->repair_status_text }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Warehouse area:') }}</label>
                                    <p class="form-control-static">
                                        <span class="badge {{ $suggestAPlaceForm->has_warehouse ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $suggestAPlaceForm->warehouse_status_text }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Parking:') }}</label>
                                    <p class="form-control-static">
                                        <span class="badge {{ $suggestAPlaceForm->has_parking ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $suggestAPlaceForm->parking_status_text }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Status:') }}</label>
                                    <p class="form-control-static">
                                        @if($suggestAPlaceForm->is_read)
                                            <span class="badge bg-success">{{ __('Read') }}</span>
                                            @if($suggestAPlaceForm->read_at)
                                                <small class="text-muted d-block">{{ __('Read at:') }} {{ $suggestAPlaceForm->read_at->format('d.m.Y H:i') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">{{ __('Unread') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Submitted:') }}</label>
                                    <p class="form-control-static">{{ $suggestAPlaceForm->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('File:') }}</label>
                                    <p class="form-control-static">
                                        @if($suggestAPlaceForm->file)
                                            <a href="{{ $suggestAPlaceForm->file_url }}" target="_blank" class="btn btn-info">
                                                <i class="material-icons-outlined">download</i>
                                                {{ $suggestAPlaceForm->file_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('No file uploaded') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Details:') }}</label>
                                    <div style="color: black !important" class="">
                                        {!! nl2br(e($suggestAPlaceForm->details)) !!}
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