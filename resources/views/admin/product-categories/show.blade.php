@extends('admin.layout.app')

@section('title', 'View Product Category')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>View Product Category</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.product-categories.index') }}">Product Categories</a>
                        </li>
                        <li class="breadcrumb-item active">View Category</li>
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
                            <h5>Product Category Details</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.product-categories.edit', $productCategory) }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">edit</i>
                                    Edit
                                </a>
                                <a href="{{ route('admin.product-categories.index') }}" class="btn btn-secondary">
                                    <i class="material-icons-outlined">arrow_back</i>
                                    Back
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Basic Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>ID:</strong></td>
                                        <td>{{ $productCategory->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($productCategory->status)
                                                <span class="badge badge-light-success">Active</span>
                                            @else
                                                <span class="badge badge-light-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Order:</strong></td>
                                        <td>{{ $productCategory->order }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td>{{ $productCategory->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated:</strong></td>
                                        <td>{{ $productCategory->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Translations -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Translations</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Language</th>
                                                <th>Title</th>
                                                <th>Slug</th>
                                                <th>SEO Title</th>
                                                <th>SEO Keywords</th>
                                                <th>SEO Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($productCategory->translates as $translate)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $translate->language->title }}</span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $translate->title ?: 'Not set' }}</strong>
                                                    </td>
                                                    <td>
                                                        <code class="text-primary">{{ $translate->slug ?: 'Not set' }}</code>
                                                    </td>
                                                    <td>
                                                        {{ $translate->seo_title ?: 'Not set' }}
                                                    </td>
                                                    <td>
                                                        @if($translate->seo_keywords)
                                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $translate->seo_keywords }}">
                                                                {{ $translate->seo_keywords }}
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Not set</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($translate->seo_description)
                                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $translate->seo_description }}">
                                                                {{ $translate->seo_description }}
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Not set</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        No translations found
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
    </div>
</div>
@endsection 