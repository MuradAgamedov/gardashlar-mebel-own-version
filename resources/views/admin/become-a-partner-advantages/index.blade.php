@extends('admin.layout.app')

@section('title', __('Become A Partner Advantages'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Become A Partner Advantages') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Become A Partner Advantages') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">{{ __('Success:') }}</h6>
                <p class="mb-0">{{ session('success') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">{{ __('Error:') }}</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5>{{ __('Become A Partner Advantages') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.become-a-partner-advantages.create') }}" class="btn btn-primary">
                                        <i class="material-icons-outlined">add</i> {{ __('Add New Advantage') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Icon') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($advantages as $advantage)
                                        @php
                                            $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                            $mainTranslate = $advantage->translates->firstWhere('language_id', $mainLanguageId)
                                                ?? $advantage->translates->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $advantage->id }}</td>
                                            <td>
                                                @if($advantage->icon)
                                                    <img src="{{ asset('storage/' . $advantage->icon) }}" 
                                                         alt="{{ __('Icon') }}" 
                                                         class="img-thumbnail" 
                                                         style="max-width: 50px; max-height: 50px;">
                                                @else
                                                    <span class="text-muted">{{ __('No icon') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mainTranslate)
                                                    <strong>{{ $mainTranslate->title }}</strong>
                                                @else
                                                    <span class="text-muted">{{ __('No title') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mainTranslate)
                                                    {{ Str::limit($mainTranslate->description, 100) }}
                                                @else
                                                    <span class="text-muted">{{ __('No description') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($advantage->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.become-a-partner-advantages.edit', $advantage->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="{{ __('Edit') }}">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.become-a-partner-advantages.destroy', $advantage->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('{{ __('Are you sure you want to delete this advantage?') }}')"
                                                                title="{{ __('Delete') }}">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                                                    <p class="mt-2">{{ __('No advantages found') }}</p>
                                                    <a href="{{ route('admin.become-a-partner-advantages.create') }}" class="btn btn-primary">
                                                        {{ __('Add First Advantage') }}
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