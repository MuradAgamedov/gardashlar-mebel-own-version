@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Qiymət Filtrləri</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Qiymət Filtrləri</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">check_circle</i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined">error</i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Qiymət Filtrləri Siyahısı</h5>
                            <a href="{{ route('admin.product-price-filters.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                Yeni Əlavə Et
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($filters->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>Başlıq</th>
                                            <th>Min Qiymət</th>
                                            <th>Max Qiymət</th>
                                            <th>Status</th>
                                            <th>Sıra</th>
                                            <th width="200">Əməliyyatlar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-list">
                                        @foreach($filters as $filter)
                                            <tr data-id="{{ $filter->id }}" class="sortable-item">
                                                <td>
                                                    <div class="drag-handle">
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <circle cx="8" cy="8" r="1"></circle>
                                                            <circle cx="16" cy="8" r="1"></circle>
                                                            <circle cx="8" cy="16" r="1"></circle>
                                                            <circle cx="16" cy="16" r="1"></circle>
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        @php
                                                            $mainTranslate = $filter->translate()->first();
                                                        @endphp
                                                        <strong>{{ $mainTranslate?->title ?? 'Başlıq yoxdur' }}</strong>
                                                        @if($mainTranslate)
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $mainTranslate->language->title ?? 'Dil yoxdur' }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($filter->min_price)
                                                        <span class="badge bg-info">{{ number_format($filter->min_price, 2) }} ₼</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($filter->max_price)
                                                        <span class="badge bg-warning">{{ number_format($filter->max_price, 2) }} ₼</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($filter->status)
                                                        <span class="badge bg-success">Aktiv</span>
                                                    @else
                                                        <span class="badge bg-danger">Passiv</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $filter->order }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.product-price-filters.edit', $filter->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Redaktə et">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </a>
                                                        <form action="{{ route('admin.product-price-filters.destroy', $filter->id) }}" 
                                                              method="POST" 
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger delete-btn" 
                                                                    title="Sil"
                                                                    data-title="{{ $mainTranslate?->title ?? 'Qiymət filtiri' }}">
                                                                <i class="material-icons-outlined">delete</i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="material-icons-outlined text-muted" style="font-size: 48px;">filter_list</i>
                                <p class="text-muted mt-2">Hələ heç bir qiymət filtiri əlavə edilməyib.</p>
                                <a href="{{ route('admin.product-price-filters.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                    İlk Filtiri Əlavə Et
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable
    const sortableList = document.getElementById('sortable-list');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(sortableList.querySelectorAll('.sortable-item')).map(item => item.dataset.id);
                
                fetch('{{ route("admin.product-price-filters.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update order numbers in the table
                        const rows = sortableList.querySelectorAll('.sortable-item');
                        rows.forEach((row, index) => {
                            const orderCell = row.querySelector('td:nth-child(6)');
                            if (orderCell) {
                                orderCell.textContent = index + 1;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }

    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const title = this.dataset.title || 'Bu qiymət filtirini';
            
            Swal.fire({
                title: 'Əminsiniz?',
                text: `${title} silmək istədiyinizə əminsiniz?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Bəli, sil!',
                cancelButtonText: 'Ləğv et'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

<style>
.drag-handle {
    cursor: move;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.drag-handle:hover {
    color: #495057;
}
</style>
@endpush
@endsection 