@extends('admin.layout.app')

@section('title', 'Product Details')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Product Details</h3>
                    <p class="text-muted">Product details management</p>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.index') }}">Products</a>
                        </li>
                        <li class="breadcrumb-item active">Product Details</li>
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
                            <h5>Product Details for: <strong>{{ $product->translate()->title ?? 'Product' }}</strong></h5>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                <i class="material-icons-outlined">arrow_back</i> Back to Products
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="material-icons-outlined">info</i>
                            <strong>Note:</strong> Product details functionality has been removed from this system.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
