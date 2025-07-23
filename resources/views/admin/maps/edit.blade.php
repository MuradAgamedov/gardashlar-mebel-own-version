@extends('admin.layout.app')

@section('title', 'Edit Map')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Map</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.maps.index') }}">Map Management</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Map</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Please fix the following errors:</h6>
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
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Map: #{{ $map->id }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.maps.update', $map) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Map -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">
                                        Map Content <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('map') is-invalid @enderror" 
                                              name="map" 
                                              rows="8"
                                              placeholder="Paste your map embed code here (e.g., Google Maps iframe, coordinates, etc.)"
                                              required>{{ old('map', $map->map) }}</textarea>
                                    @error('map')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        You can paste Google Maps embed code, coordinates, or any map-related content here.
                                    </small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="status" 
                                               name="status" 
                                               value="1" 
                                               {{ old('status', $map->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Info -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Current Order</label>
                                    <input type="text" class="form-control" value="{{ $map->order }}" readonly>
                                    <small class="form-text text-muted">
                                        Order can be changed by dragging and dropping in the maps list.
                                    </small>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Update Map
                                        </button>
                                        <a href="{{ route('admin.maps.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">cancel</i>
                                            Cancel
                                        </a>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on map textarea
    const mapTextarea = document.querySelector('textarea[name="map"]');
    if (mapTextarea) {
        mapTextarea.focus();
    }
});
</script>
@endpush 