@extends('admin.layout.app')

@section('title', __('Edit Social Network'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Edit Social Network') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i> {{ __('Home') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.social-networks.index') }}">{{ __('Social Networks') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">{{ __('Error occurred:') }}</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">{{ __('Error occurred:') }}</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Edit Social Network') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.social-networks.update', $socialNetwork->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Type Selection -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('Social Network Type') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            name="type" 
                                            id="type-select" 
                                            required>
                                        <option value="">{{ __('Select a social network') }}</option>
                                        @foreach($types as $key => $type)
                                            <option value="{{ $key }}" 
                                                    data-icon="{{ $type['icon'] }}" 
                                                    data-color="{{ $type['color'] }}"
                                                    {{ old('type', $socialNetwork->type) == $key ? 'selected' : '' }}>
                                                {{ $type['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- URL -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ __('URL') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="url" 
                                           class="form-control @error('url') is-invalid @enderror" 
                                           name="url" 
                                           value="{{ old('url', $socialNetwork->url) }}" 
                                           placeholder="{{ __('Enter social network URL (e.g., https://facebook.com/yourpage)') }}" 
                                           required>
                                    @error('url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="status" 
                                               id="status" 
                                               {{ old('status', $socialNetwork->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">{{ __('Check to make this social network visible') }}</small>
                                </div>
                            </div>

                            <!-- Preview -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Preview') }}</label>
                                    <div id="preview" class="border rounded p-3 text-center" style="display: none;">
                                        <i id="preview-icon" class="" style="font-size: 32px; margin-bottom: 10px;"></i>
                                        <br>
                                        <strong id="preview-name"></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.social-networks.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">arrow_back</i>
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type-select');
    const preview = document.getElementById('preview');
    const previewIcon = document.getElementById('preview-icon');
    const previewName = document.getElementById('preview-name');

    function updatePreview() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        if (typeSelect.value) {
            const icon = selectedOption.dataset.icon;
            const color = selectedOption.dataset.color;
            const name = selectedOption.text;

            previewIcon.className = icon;
            previewIcon.style.color = color;
            previewName.textContent = name;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }

    typeSelect.addEventListener('change', updatePreview);

    // Initial preview
    updatePreview();
});
</script>
@endsection