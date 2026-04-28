@extends('admin.layout.app')

@section('title', 'Edit Currency')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Currency</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.currencies.index') }}">Currencies</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Error occurred:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Error occurred:</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Currency</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.currencies.update', $currency->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Currency Code -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Currency Code <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           name="code" 
                                           value="{{ old('code', $currency->code) }}" 
                                           placeholder="Enter currency code (e.g., USD, EUR, AZN)" 
                                           maxlength="3"
                                           style="text-transform: uppercase;"
                                           required>
                                    <small class="form-text text-muted">3-letter currency code (e.g., USD, EUR, AZN)</small>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Exchange Rate -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Exchange Rate <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('course') is-invalid @enderror" 
                                           name="course" 
                                           value="{{ old('course', $currency->course) }}" 
                                           step="0.0001"
                                           min="0.0001"
                                           max="999999.9999"
                                           placeholder="Enter exchange rate (e.g., 1.7000)" 
                                           required>
                                    <small class="form-text text-muted">Exchange rate relative to base currency (AZN)</small>
                                    @error('course')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="status" 
                                               id="status" 
                                               value="1"
                                               {{ old('status', $currency->status ? '1' : '0') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Check to make this currency visible</small>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">
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
    // Auto-uppercase currency code
    const codeInput = document.querySelector('input[name="code"]');
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});
</script>
@endsection 