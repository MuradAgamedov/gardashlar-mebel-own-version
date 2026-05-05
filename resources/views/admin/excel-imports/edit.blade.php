@extends('admin.layout.app')

@section('title', 'Excel Fayl Yükləmə')

@section('content')
<div class="content-body">
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <div class="welcome-text">
                <h4>Excel Fayl Yükləmə</h4>
            </div>
        </div>
        <div class="col p-md-0 justify-content-end d-flex align-items-center">
            <div class="breadcrumb-right">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
                        <li class="breadcrumb-item active">Excel Fayl Yükləmə</li>
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
                        <h4 class="card-title">Excel Fayl Yüklə</h4>
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

                        <form action="{{ route('admin.excel-imports.update', $excelImport?->id ?? 1) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Excel Fayl</label>
                                <input type="file" class="form-control" id="excel_file" name="excel_file" 
                                       accept=".xlsx,.xls" required>
                                <div class="form-text">Yalnız .xlsx və .xls formatında fayllar qəbul edilir (Maksimum: 10MB)</div>
                            </div>

                            @if($excelImport && $excelImport->file_url)
                                <div class="mb-3">
                                    <label class="form-label">Mövcud Fayl</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('admin.excel-imports.download') }}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-download"></i> Faylı Yüklə
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete()">
                                            <i class="fas fa-trash"></i> Faylı Sil
                                        </button>
                                        <small class="text-muted">{{ $excelImport->file_url }}</small>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Yüklə
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Geri
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" action="{{ route('admin.excel-imports.destroy', $excelImport?->id ?? 1) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete() {
    if (confirm('Excel faylını silmək istədiyinizə əminsiniz? Bu əməliyyat geri qaytarıla bilməz.')) {
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
            alert('Fayl ölçüsü 10MB-dan böyük ola bilməz!');
            e.target.value = '';
            return;
        }
        
        if (!allowedTypes.includes(file.type)) {
            alert('Yalnız Excel faylları (.xlsx, .xls) qəbul edilir!');
            e.target.value = '';
            return;
        }
    }
});
</script>
@endsection
