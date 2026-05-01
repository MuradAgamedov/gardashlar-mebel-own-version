@extends('admin.layout.app')

@section('title', __('Dictionary'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>
                        <i class="material-icons-outlined">translate</i>
                        {{ __('Dictionary Management') }}
                    </h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="material-icons-outlined">translate</i>
                            {{ __('Dictionary Management') }}
                        </li>
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
                            <h5>
                                <i class="material-icons-outlined">translate</i>
                                {{ __('Dictionary Items') }}
                            </h5>
                            <a href="{{ route('admin.dictionaries.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                        
                        @if(request('search'))
                            <div class="mt-2">
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="material-icons-outlined">info</i>
                                    <strong>{{ __('Search Results:') }}</strong>
                                    <span class="badge bg-primary me-2">{{ __('Keyword/Translation: ":search"', ['search' => request('search')]) }}</span>
                                    <span class="badge bg-secondary">{{ __(':count result(s)', ['count' => $dictionaries->count()]) }}</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Search Form -->
                        <div class="mt-3">
                            <form action="{{ route('admin.dictionaries.index') }}" method="GET" class="row g-3">
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="material-icons-outlined">search</i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               name="search" 
                                               value="{{ request('search') }}"
                                               placeholder="Search by keyword or translation in any language...">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-outline-primary" title="Search" style="width: 50px; height: 50px;">
                                        <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">search</i>
                                    </button>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('admin.dictionaries.index') }}" class="btn btn-outline-secondary" title="Clear" style="width: 50px; height: 50px;">
                                        <i class="material-icons-outlined" style="font-size: 24px; line-height: 1.5;">clear</i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="200">
                                            <i class="material-icons-outlined">key</i>
                                            {{ __('Keyword') }}
                                        </th>
                                        <th>
                                            <i class="material-icons-outlined">translate</i>
                                            {{ __('Translations') }}
                                        </th>
                                        <th width="150">
                                            <i class="material-icons-outlined">settings</i>
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dictionaries as $dictionary)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">translate</i>
                                                    <strong>{{ $dictionary->keyword }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @if($dictionary->translates->count() > 0)
                                                        @foreach($dictionary->translates as $translation)
                                                            <span class="badge bg-primary text-white">
                                                                <strong>{{ $translation->language->lang_code }}:</strong>
                                                                {{ Str::limit($translation->value, 30) }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="material-icons-outlined" style="font-size: 12px;">warning</i>
                                                            {{ __('No translations') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.dictionaries.edit', $dictionary->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.dictionaries.destroy', $dictionary->id) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to delete this dictionary item?')"
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 64px; color: #6c757d;">translate</i>
                                                    <h5 class="mt-3">No Dictionary Items Found</h5>
                                                    <p>Start by adding dictionary items for translations.</p>
                                                    <a href="{{ route('admin.dictionaries.create') }}" class="btn btn-primary">
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
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.75em;
    }
    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .table th i {
        margin-right: 5px;
        vertical-align: middle;
    }
    .card-header h5 i {
        margin-right: 8px;
        vertical-align: middle;
    }
    .page-title h3 i {
        margin-right: 10px;
        vertical-align: middle;
    }
    .breadcrumb-item.active i {
        margin-right: 5px;
        vertical-align: middle;
    }
</style>
@endpush 