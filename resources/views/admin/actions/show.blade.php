@extends('admin.layout.app')

@section('title', __('View Action'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ __('Action Details') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.actions.products', $action) }}" class="btn btn-warning" title="{{ __('Manage Products') }}">
                                <i class="material-icons-outlined">inventory</i>
                            </a>
                            <a href="{{ route('admin.actions.edit', $action) }}" class="btn btn-primary" title="{{ __('Edit') }}">
                                <i class="material-icons-outlined">edit</i>
                            </a>
                            <a href="{{ route('admin.actions.index') }}" class="btn btn-secondary" title="{{ __('Back to Actions') }}">
                                <i class="material-icons-outlined">arrow_back</i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Basic Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td>{{ $action->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Company') }}:</strong></td>
                                            <td>{{ $action->company->translates->firstWhere('language_id', \App\Models\Language::getMainLanguage()->id)?->title ?? __('No Company') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Icon') }}:</strong></td>
                                            <td>
                                                @if($action->icon)
                                                    <i class="material-icons-outlined">{{ $action->icon }}</i>
                                                    <span class="ms-2">{{ $action->icon }}</span>
                                                @else
                                                    <span class="text-muted">{{ __('No Icon') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Status') }}:</strong></td>
                                            <td>
                                                <span class="badge {{ $action->status ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $action->status ? __('Active') : __('Inactive') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Start Date') }}:</strong></td>
                                            <td>{{ $action->start_date ? $action->start_date->format('Y-m-d') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('End Date') }}:</strong></td>
                                            <td>{{ $action->end_date ? $action->end_date->format('Y-m-d') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Order') }}:</strong></td>
                                            <td>{{ $action->order }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Created') }}:</strong></td>
                                            <td>{{ $action->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Updated') }}:</strong></td>
                                            <td>{{ $action->updated_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Translations -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Translations') }}</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($action->translates as $translate)
                                        <div class="mb-4 p-3 border rounded">
                                            <h6 class="text-primary">{{ $translate->language->name }}</h6>
                                            
                                            <div class="mb-3">
                                                <strong>{{ __('Title') }}:</strong>
                                                <p class="mb-0">{{ $translate->title }}</p>
                                            </div>
                                            
                                            @if($translate->description)
                                                <div class="mb-3">
                                                    <strong>Description:</strong>
                                                    <p class="mb-0">{{ $translate->description }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($translate->image)
                                                <div class="mb-3">
                                                    <strong>Image:</strong>
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $translate->image) }}" 
                                                             alt="{{ $translate->image_alt_text ?? 'Action Image' }}" 
                                                             class="img-fluid" 
                                                             style="max-height: 200px;">
                                                    </div>
                                                    @if($translate->image_alt_text)
                                                        <p class="text-muted small mt-1">{{ $translate->image_alt_text }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products in Action -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Products in this Action') }} ({{ $action->companyProducts->count() }})</h5>
                                </div>
                                <div class="card-body">
                                    @if($action->companyProducts->count() > 0)
                                        <div class="row">
                                            @foreach($action->companyProducts as $companyProduct)
                                                @php
                                                    $product = $companyProduct->product;
                                                    $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                    $mainTranslate = $product?->translates->firstWhere('language_id', $mainLanguageId);
                                                @endphp
                                                <div class="col-md-3 col-lg-2 mb-3">
                                                    <div class="card border-success">
                                                        @if($mainTranslate && $mainTranslate->image)
                                                            <img src="{{ asset('storage/' . $mainTranslate->image) }}" 
                                                                 class="card-img-top" 
                                                                 alt="{{ $mainTranslate->image_alt_text ?? __('Product Image') }}"
                                                                 style="height: 120px; object-fit: cover;">
                                                        @else
                                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                                 style="height: 120px;">
                                                                <i class="material-icons-outlined text-muted">image</i>
                                                            </div>
                                                        @endif
                                                        
                                                        <div class="card-body p-2">
                                                            <h6 class="card-title small">{{ $mainTranslate->title ?? __('No Title') }}</h6>
                                                            <span class="badge bg-success small">
                                                                <i class="material-icons-outlined" style="font-size: 0.8rem;">local_offer</i>
                                                                {{ __('In Action') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="material-icons-outlined text-muted" style="font-size: 3rem;">inventory</i>
                                            <h6 class="text-muted mt-2">{{ __('No products in this action') }}</h6>
                                            <a href="{{ route('admin.actions.products', $action) }}" class="btn btn-primary btn-sm">
                                                <i class="material-icons-outlined">add</i>
                                                {{ __('Add Products') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
