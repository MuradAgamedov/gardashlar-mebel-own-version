@extends('admin.layout.app')

@section('title', 'Detail - ' . ($detailValue->translate()->keyword ?? 'Detail Value'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Detail Information</h3>
                    <p class="text-muted">
                        {{ $product->translate()->title ?? 'Product' }} > 
                        {{ $detailValue->translate()->keyword ?? 'Detail Value' }}
                    </p>
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.detail-values.index', $product) }}">Detail Values</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.products.detail-values.details.index', [$product, $detailValue]) }}">Details</a>
                        </li>
                        <li class="breadcrumb-item active">View</li>
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
                            <h5>Detail Information</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.products.detail-values.details.edit', [$product, $detailValue, $detail]) }}" class="btn btn-outline-primary">
                                    <i class="material-icons-outlined">edit</i> Edit
                                </a>
                                <a href="{{ route('admin.products.detail-values.details.index', [$product, $detailValue]) }}" class="btn btn-outline-secondary">
                                    <i class="material-icons-outlined">arrow_back</i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Translations</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($detail->translates->count() > 0)
                                            @foreach($detail->translates as $translate)
                                                <div class="border rounded p-3 mb-3">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="form-label fw-bold">Language:</label>
                                                            <div class="badge badge-light-primary fs-6">
                                                                {{ $translate->language->lang_code ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">Keyword:</label>
                                                            <div class="fs-6">{{ $translate->keyword }}</div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="form-label fw-bold">Value:</label>
                                                            <div class="fs-6">{{ $translate->value }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">translate</i>
                                                    <p class="mt-2">No translations found</p>
                                                </div>
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
</div>
@endsection
