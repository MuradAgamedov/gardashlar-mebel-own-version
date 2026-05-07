@extends('admin.layout.app')

@section('title', __('excel_import_title'))

@section('content')
<div class="content-body">
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <div class="welcome-text">
                <h4>{{ __('excel_import_title') }}</h4>
            </div>
        </div>
        <div class="col p-md-0 justify-content-end d-flex align-items-center">
            <div class="breadcrumb-right">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('excel_import_title') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('excel_upload_title') }}</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.excel-imports.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="excel_file" class="form-label">{{ __('excel_file_label') }}</label>
                                <input type="file" class="form-control" id="excel_file" name="excel_file" 
                                       accept=".xlsx,.xls" required>
                                <div class="form-text">{{ __('excel_file_help') }}</div>
                            </div>

                            @if($excelImport && $excelImport->file_url)
                                <div class="mb-3">
                                    <label class="form-label">{{ __('existing_file_label') }}</label>
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <small class="text-muted flex-grow-1">{{ $excelImport->file_url }}</small>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.excel-imports.download') }}" class="btn btn-sm btn-info" target="_blank" title="{{ __('download_file_title') }}">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete()" title="{{ __('delete_file_title') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" title="{{ __('upload_title') }}">
                                    <i class="fas fa-upload"></i>
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" title="{{ __('back_title') }}">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" action="{{ route('admin.excel-imports.destroy') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete() {
    if (confirm('{{ __('excel_delete_confirm') }}')) {
        document.getElementById('deleteForm').submit();
    }
}

// File validation
document.getElementById('excel_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const maxSize = 10 * 1024 * 1024; // 10MB
    const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
    
    if (file) {
        if (file.size > maxSize) {
            alert('{{ __('excel_file_size_error') }}');
            e.target.value = '';
            return;
        }
        
        if (!allowedTypes.includes(file.type)) {
            alert('{{ __('excel_file_type_error') }}');
            e.target.value = '';
            return;
        }
    }
});
</script>
@endsection
