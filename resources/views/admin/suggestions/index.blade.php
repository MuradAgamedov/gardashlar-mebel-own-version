@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Tövsiyyələr</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Tövsiyyələr</li>
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
                            <h5>Bütün Tövsiyyələr</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-danger" id="deleteSelectedBtn" style="display: none;">
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                                <a href="{{ route('admin.suggestions.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search Section -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Tövsiyyə axtar...">
                                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                        <i class="material-icons-outlined">search</i>
                                    </button>
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn">
                                        <i class="material-icons-outlined">clear</i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">
                                        Hamısını Seç
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input" id="selectAllHeader">
                                        </th>
                                        <th width="50">Sıra</th>
                                        <th width="80">Şəkil</th>
                                        <th>Başlıq</th>
                                        <th>Dillər</th>
                                        <th width="80">Status</th>
                                        <th width="150">Əməliyyatlar</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-suggestions">
                                    @forelse($suggestions as $suggestion)
                                        <tr data-id="{{ $suggestion->id }}" class="suggestion-row">
                                            <td>
                                                <input type="checkbox" class="form-check-input suggestion-checkbox" value="{{ $suggestion->id }}">
                                            </td>
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
                                                @if($suggestion->image)
                                                    <img src="{{ asset('storage/' . $suggestion->image) }}" 
                                                         alt="{{ $suggestion->translate()?->image_alt_text }}" 
                                                         class="img-thumbnail" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="material-icons-outlined text-muted">image</i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $firstTranslation = $suggestion->translates->first();
                                                @endphp
                                                {{ $firstTranslation ? $firstTranslation->title : 'Tərcümə yoxdur' }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @foreach($suggestion->translates as $translate)
                                                        <span class="badge bg-primary">{{ $translate->language->lang_code }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                @if($suggestion->status)
                                                    <span class="badge bg-success">Aktiv</span>
                                                @else
                                                    <span class="badge bg-secondary">Deaktiv</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.suggestions.edit', $suggestion) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : 'Tövsiyyə #' . $suggestion->id }}"
                                                            data-url="{{ route('admin.suggestions.destroy', $suggestion) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">lightbulb</i>
                                                    <p class="mt-2">Tövsiyyə tapılmadı</p>
                                                    <a href="{{ route('admin.suggestions.create') }}" class="btn btn-primary">
                                                        <i class="material-icons-outlined">add</i>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const suggestionRows = document.querySelectorAll('.suggestion-row');

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        suggestionRows.forEach(row => {
            const title = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const languages = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || languages.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchBtn.addEventListener('click', performSearch);
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        suggestionRows.forEach(row => {
            row.style.display = '';
        });
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const suggestionCheckboxes = document.querySelectorAll('.suggestion-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

    function updateDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.suggestion-checkbox:checked');
        deleteSelectedBtn.style.display = checkedBoxes.length > 0 ? 'inline-block' : 'none';
    }

    function selectAll(checked) {
        suggestionCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        updateDeleteButton();
    }

    selectAllCheckbox.addEventListener('change', function() {
        selectAll(this.checked);
    });

    selectAllHeader.addEventListener('change', function() {
        selectAll(this.checked);
    });

    suggestionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDeleteButton);
    });

    // Delete selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.suggestion-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

        if (selectedIds.length === 0) {
            Swal.fire({
                title: 'Xəbərdarlıq!',
                text: 'Heç bir tövsiyyə seçilməyib',
                icon: 'warning'
            });
            return;
        }

        Swal.fire({
            title: 'Əminsiniz?',
            text: `${selectedIds.length} tövsiyyəni silmək istəyirsiniz?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Bəli, sil!',
            cancelButtonText: 'Ləğv et'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.suggestions.deleteSelected") }}';
                form.innerHTML = `
                    @csrf
                    ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Drag and drop reordering
    const sortableList = document.getElementById('sortable-suggestions');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const suggestions = Array.from(sortableList.children).map((row) => row.getAttribute('data-id'));

                fetch('{{ route("admin.suggestions.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ orders: suggestions })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire({
                            title: 'Xəta!',
                            text: data.message || 'Tövsiyyələri yenidən sıralamaq mümkün olmadı',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }

    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');

            Swal.fire({
                title: 'Əminsiniz?',
                text: `"${title}" tövsiyyəsini silmək istəyirsiniz?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Bəli, sil!',
                cancelButtonText: 'Ləğv et'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>

<style>
.drag-handle {
    cursor: grab;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
}
.drag-handle:active { cursor: grabbing; }
.sortable-ghost { opacity: 0.5; background-color: #f8f9fa; }
.sortable-chosen { background-color: #e3f2fd; }
</style>
@endpush
