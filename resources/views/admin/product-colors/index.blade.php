@extends('admin.layout.app')

@section('title', 'Product Colors')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Colors</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Product Colors</li>
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
                            <h5>Product Colors</h5>
                            <a href="{{ route('admin.product-colors.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                Add Color
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Color Preview</th>
                                        <th>Color Code</th>
                                        <th>Created At</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productColors as $productColor)
                                        <tr>
                                            <td>{{ $productColor->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="color-preview me-3" 
                                                         style="width: 40px; height: 40px; border-radius: 8px; background-color: {{ $productColor->color_code }}; border: 2px solid #ddd;">
                                                    </div>
                                                    <span class="fw-bold">{{ $productColor->color_code }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $productColor->color_code }}</code>
                                            </td>
                                            <td>
                                                {{ $productColor->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.product-colors.edit', $productColor) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.product-colors.destroy', $productColor) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this color?')">
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
                                            <td colspan="5" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">palette</i>
                                                    <p class="mt-2">No colors added yet</p>
                                                    <a href="{{ route('admin.product-colors.create') }}" class="btn btn-primary">
                                                        Add First Color
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