@extends('admin.layout.app')

@section('title', 'Edit Project Video')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Edit Project Video</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.projects.index') }}">Projects Management</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.projects.videos.index', $project) }}">Project Videos</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Video</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <h6 class="alert-heading mb-1">{{ __('Please fix the following errors:') }}</h6>
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
                        <h5>Edit Video for Project: 
                            @php
                                $firstTranslation = $project->translates->first();
                            @endphp
                            <span class="text-primary">{{ $firstTranslation ? $firstTranslation->title : 'Project #' . $project->id }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.projects.videos.update', [$project, $video]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Video Link -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        Video Link <span class="text-danger">*</span>
                                    </label>
                                    <input type="url" 
                                           class="form-control @error('video_link') is-invalid @enderror" 
                                           name="video_link" 
                                           value="{{ old('video_link', $video->video_link) }}" 
                                           placeholder="Enter video URL (YouTube, Vimeo, etc.)"
                                           required>
                                    @error('video_link')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Enter the full URL of the video (e.g., https://www.youtube.com/watch?v=...)
                                    </small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div style="padding: 15px" class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="status" 
                                               name="status" 
                                               value="1" 
                                               {{ old('status', $video->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.projects.videos.index', $project) }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">cancel</i>
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