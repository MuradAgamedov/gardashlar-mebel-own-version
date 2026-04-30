@extends('admin.layout.app')

@section('title', __('Edit Credit Card'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Edit Credit Card') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.credit-cards.index') }}">{{ __('Credit Cards') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Success:</h6>
                <p class="mb-0">{{ session('success') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <h6 class="alert-heading">Validation Errors:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Edit Credit Card') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.credit-cards.update', $creditCard->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Title --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" 
                                       value="{{ old('title', $creditCard->title) }}" required>
                            </div>

                            {{-- Months --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Months (e.g. 3, 6, 9, 12)') }}</label>
                                <input type="text" name="months" class="form-control" 
                                       placeholder="Enter installment months (comma separated)"
                                       value="{{ old('months', $creditCard->months) }}">
                                <small class="form-text text-muted">
                                    Məsələn: <strong>3, 6, 9, 12</strong> (vergüllə ayırın)
                                </small>
                            </div>

                            {{-- Image Upload --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('Image') }}</label>
                                <input type="file" name="image" class="form-control" id="imageInput">
                                <small class="form-text text-muted">
                                    Allowed types: jpg, jpeg, png, gif, webp (max 2MB)
                                </small>
                            </div>

                            {{-- Preview --}}
                            <div class="mb-3">
                                <label class="form-label d-block">Preview:</label>
                                <img id="imagePreview" 
                                     src="{{ $creditCard->image ? asset('storage/'.$creditCard->image) : '#' }}" 
                                     alt="Preview"
                                     style="max-height: 150px; border:1px solid #ddd; padding:4px; border-radius:6px; {{ $creditCard->image ? '' : 'display:none;' }}">
                            </div>

                            {{-- Submit --}}
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.credit-cards.index') }}" class="btn btn-secondary">
                                    <i class="material-icons-outlined">arrow_back</i> 
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="material-icons-outlined">save</i> 
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS for live preview --}}
<script>
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const [file] = this.files;
        const preview = document.getElementById('imagePreview');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });
</script>
@endsection
