@extends('admin.layout.app')

@section('title', 'Services')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Services</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Services</li>
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
                            <h5>All Services</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        </div>
                        
                        @if(request('search'))
                            <div class="mt-2">
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="material-icons-outlined">info</i>
                                    <strong>Search Results:</strong>
                                    <span class="badge bg-primary me-2">Title/Description: "{{ request('search') }}"</span>
                                    <span class="badge bg-secondary">{{ $services->count() }} result(s)</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Search Form -->
                        <div class="mt-3">
                            <form action="{{ route('admin.services.index') }}" method="GET" class="row g-3">
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="material-icons-outlined">search</i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               name="search" 
                                               value="{{ request('search') }}"
                                               placeholder="Search by title or description...">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-outline-primary" title="Search" style="width: 50px; height: 50px;">
                                        <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">search</i>
                                    </button>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary" title="Clear" style="width: 50px; height: 50px;">
                                        <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">clear</i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Title</th>
                                        <th>Card Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-services">
                                    @forelse($services as $service)
                                        <tr data-id="{{ $service->id }}">
                                            <td>
                                                @if($service->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                    $mainTranslate = $service->translates->firstWhere('language_id', $mainLanguageId)
                                                        ?? $service->translates->first();
                                                @endphp
                                                {{ $mainTranslate ? $mainTranslate->title : 'No title' }}
                                            </td>
                                            <td>
                                                @if($mainTranslate)
                                                    {{ Str::limit($mainTranslate->card_description, 100) }}
                                                @else
                                                    No description
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.service-advantages.index', $service->id) }}" 
                                                       class="btn btn-sm btn-info">
                                                        Advantages
                                                    </a>
                                                    <a href="{{ route('admin.services.edit', $service->id) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        Edit
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No services found.</td>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // No JavaScript functionality needed - status is display-only
});
</script>
@endpush
@endsection 