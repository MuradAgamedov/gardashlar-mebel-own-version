@extends("admin.layout.app")

@section("content")
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h2>All SEO Pages Settings</h2>
                    <p class="mb-0 text-title-gray">Manage global SEO settings for all pages</p>
                </div>
                <div class="col-sm-6 col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="iconly-Home icli svg-color"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">SEO Management</li>
                        <li class="breadcrumb-item active">All SEO Pages</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>SEO Pages Settings</h4>
                        <p class="text-muted mb-0">Configure global SEO settings that apply to all pages</p>
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

                        <form action="{{ route('admin.all-seo-pages.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- SEO Checkboxes -->
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 style="color: black" class="mb-0">SEO Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Is Indexed -->
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="is_indexed" 
                                                       name="is_indexed" 
                                                       value="1"
                                                       {{ old('is_indexed', $seoPages->is_indexed) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_indexed">
                                                    <strong>Is Indexed</strong>
                                                    <small class="d-block text-muted">Allow search engines to index pages</small>
                                                </label>
                                            </div>

                                            <!-- Is Followed -->
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="is_followed" 
                                                       name="is_followed" 
                                                       value="1"
                                                       {{ old('is_followed', $seoPages->is_followed) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_followed">
                                                    <strong>Is Followed</strong>
                                                    <small class="d-block text-muted">Allow search engines to follow links</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                              {{--
                                
                                  <!-- Page Header -->
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 style="color: black" class="mb-0">Page Header Code</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="page_header" class="form-label">
                                                    <strong>Page Header</strong>
                                                    <small class="text-muted">(HTML/CSS/JS code for &lt;head&gt; section)</small>
                                                </label>
                                                <textarea class="form-control @error('page_header') is-invalid @enderror" 
                                                          id="page_header" 
                                                          name="page_header" 
                                                          rows="8" 
                                                          placeholder="Enter HTML, CSS, or JavaScript code for page header...">{{ old('page_header', $seoPages->page_header) }}</textarea>
                                                @error('page_header')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                --}}
                            </div>

                            {{--
                                
                                <div class="row mt-3">
                                <!-- Page Footer -->
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 style="color: black" class="mb-0">Page Footer Code</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="page_footer" class="form-label">
                                                    <strong>Page Footer</strong>
                                                    <small class="text-muted">(HTML/CSS/JS code for before &lt;/body&gt; tag)</small>
                                                </label>
                                                <textarea class="form-control @error('page_footer') is-invalid @enderror" 
                                                          id="page_footer" 
                                                          name="page_footer" 
                                                          rows="10" 
                                                          placeholder="Enter HTML, CSS, or JavaScript code for page footer...">{{ old('page_footer', $seoPages->page_footer) }}</textarea>
                                                @error('page_footer')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                --}}

                            <!-- mb-0 Button -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>
                                        </button>
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

<style>
.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.card.border {
    border: 1px solid #dee2e6 !important;
}

.card-header.bg-light {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
}

textarea.form-control {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.4;
}

.form-check-label small {
    font-size: 0.875rem;
    margin-top: 2px;
}

.btn-primary.btn-lg {
    padding: 12px 30px;
    font-size: 16px;
    font-weight: 600;
}
</style>
@endsection
