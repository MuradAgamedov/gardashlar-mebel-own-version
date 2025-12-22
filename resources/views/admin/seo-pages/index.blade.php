@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>SEO Pages Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">SEO Pages</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">check_circle</i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">error</i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>SEO Pages List</h5>
                            <a href="{{ route('admin.seo-pages.create') }}" class="btn btn-primary" title="Add New SEO Page">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Slug</th>
                                        <th>Status</th>
                                        <th>Languages</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($seoPages as $seoPage)
                                        <tr>
                                            <td>{{ $seoPage->id }}</td>
                                            <td>
                                                @if($seoPage->translates->isNotEmpty())
                                                    <div class="translation-list">
                                                        @foreach($seoPage->translates as $translate)
                                                            <div class="translation-item">
                                                                <span class="badge bg-info me-1">{{ $translate->language->lang_code }}</span>
                                                                <span>{{ $translate->title }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">No translation</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($seoPage->category && $seoPage->category->translates->isNotEmpty())
                                                    <div class="translation-list">
                                                        @foreach($seoPage->category->translates as $translate)
                                                            <div class="translation-item">
                                                                <span class="badge bg-secondary me-1">{{ $translate->language->lang_code }}</span>
                                                                <span>{{ $translate->title }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">No category</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($seoPage->translates->isNotEmpty())
                                                    <div class="slug-list">
                                                        @foreach($seoPage->translates as $translate)
                                                            <div class="slug-item">
                                                                <span class="badge bg-light text-dark me-1">{{ $translate->language->lang_code }}</span>
                                                                <code>{{ $translate->slug }}</code>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($seoPage->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @foreach($seoPage->translates as $translate)
                                                        <span class="badge bg-info">{{ $translate->language->lang_code }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>{{ $seoPage->created_at->format('d.m.Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.seo-pages.edit', $seoPage->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.seo-pages.destroy', $seoPage->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this SEO page?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="material-icons-outlined mb-2" style="font-size: 48px; color: #ccc;">description</i>
                                                    <p class="text-muted mb-0">No SEO pages found</p>
                                                    <a href="{{ route('admin.seo-pages.create') }}" class="btn btn-primary btn-sm mt-2" title="Create First SEO Page">
                                                        <i class="material-icons-outlined">add</i>
                                                    </a>
                                                </div>
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

@push('styles')
<style>
.translation-list {
    max-height: 120px;
    overflow-y: auto;
}

.translation-item {
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.translation-item:last-child {
    margin-bottom: 0;
}

.slug-list {
    max-height: 100px;
    overflow-y: auto;
}

.slug-item {
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.slug-item:last-child {
    margin-bottom: 0;
}

.translation-item span:not(.badge),
.slug-item span:not(.badge) {
    font-size: 0.85em;
}

.translation-item .badge,
.slug-item .badge {
    min-width: 35px;
    text-align: center;
    font-size: 0.7em;
}
</style>
@endpush
