@extends('admin.layout.app')

@section('title', 'Simple Images')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Simple Images</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Simple Images</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Simple Images</h5>
                            <a href="{{ route('admin.simple-images.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Keyword</th>
                                        <th style="width: 15%;">Image</th>
                                        <th style="width: 45%;">Go URLs</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($images as $image)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-primary me-2" style="font-size: 18px;">tag</i>
                                                    <div>
                                                        <strong class="text-dark">{{ $image->keyword }}</strong>
                                                        <br>
                                                        <small class="text-muted">ID: {{ $image->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($image->image_path)
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                             alt="{{ $image->keyword }}" 
                                                             class="img-thumbnail" 
                                                             style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                                        <a href="{{ asset('storage/' . $image->image_path) }}" 
                                                           target="_blank" 
                                                           class="position-absolute top-0 end-0 bg-primary text-white rounded-circle p-1"
                                                           style="width: 20px; height: 20px; font-size: 10px; text-decoration: none;">
                                                            <i class="material-icons-outlined" style="font-size: 12px;">open_in_new</i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="text-center">
                                                        <i class="material-icons-outlined text-muted" style="font-size: 40px;">image_not_supported</i>
                                                        <br>
                                                        <small class="text-muted">No image</small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($image->translates as $translation)
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-primary me-2" style="min-width: 30px;">
                                                                {{ strtoupper($translation->language->lang_code) }}
                                                            </span>
                                                            <a href="{{ $translation->image_go_url }}" 
                                                               target="_blank" 
                                                               class="text-decoration-none text-primary"
                                                               title="{{ $translation->image_go_url }}">
                                                                {{ Str::limit($translation->image_go_url, 40) }}
                                                                <i class="material-icons-outlined" style="font-size: 14px;">open_in_new</i>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.simple-images.edit', $image->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.simple-images.destroy', $image->id) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to delete this image?')"
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Delete">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="material-icons-outlined" style="font-size: 48px; color: #6c757d;">image</i>
                                                <h5 class="text-muted mt-3">No Simple Images</h5>
                                                <p class="text-muted">Start by adding simple images with keywords.</p>
                                                <a href="{{ route('admin.simple-images.create') }}" class="btn btn-primary">
                                                    <i class="material-icons-outlined">add</i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 