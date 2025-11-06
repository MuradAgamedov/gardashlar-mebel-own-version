@extends('admin.layout.app')

@section('title', 'Vacancies')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Vacancies</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('admin/assets/svg/iconly-sprite.svg#stroke-home') }}"></use>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Vacancies</li>
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
                            <h5>All Vacancies</h5>
                            <a href="{{ route('admin.vacancies.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                Add New Vacancy
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">Order</th>
                                        <th>Title</th>
                                        <th>Position</th>
                                        <th>Experience</th>
                                        <th>Address</th>
                                        <th>Last Apply Date</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-vacancies">
                                    @forelse($vacancies as $vacancy)
                                        <tr data-id="{{ $vacancy->id }}">
                                            <td class="drag-handle" style="cursor: move;">
                                                <div class="d-flex align-items-center">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8 6C8 7.10457 7.10457 8 6 8C4.89543 8 4 7.10457 4 6C4 4.89543 4.89543 4 6 4C7.10457 4 8 4.89543 8 6Z" fill="#6c757d"/>
                                                        <path d="M8 12C8 13.1046 7.10457 14 6 14C4.89543 14 4 13.1046 4 12C4 10.8954 4.89543 10 6 10C7.10457 10 8 10.8954 8 12Z" fill="#6c757d"/>
                                                        <path d="M8 18C8 19.1046 7.10457 20 6 20C4.89543 20 4 19.1046 4 18C4 16.8954 4.89543 16 6 16C7.10457 16 8 16.8954 8 18Z" fill="#6c757d"/>
                                                        <path d="M20 6C20 7.10457 19.1046 8 18 8C16.8954 8 16 7.10457 16 6C16 4.89543 16.8954 4 18 4C19.1046 4 20 4.89543 20 6Z" fill="#6c757d"/>
                                                        <path d="M20 12C20 13.1046 19.1046 14 18 14C16.8954 14 16 13.1046 16 12C16 10.8954 16.8954 10 18 10C19.1046 10 20 10.8954 20 12Z" fill="#6c757d"/>
                                                        <path d="M20 18C20 19.1046 19.1046 20 18 20C16.8954 20 16 19.1046 16 18C16 16.8954 16.8954 16 18 16C19.1046 16 20 16.8954 20 18Z" fill="#6c757d"/>
                                                    </svg>
                                                    <span class="ms-2">{{ $vacancy->order }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $mainTranslation = $vacancy->translates->where('language.is_default', true)->first() 
                                                        ?? $vacancy->translates->first();
                                                @endphp
                                                <strong>{{ $mainTranslation->title ?? 'N/A' }}</strong>
                                                @if($vacancy->is_expired)
                                                    <span class="badge bg-danger ms-2">Expired</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $mainTranslation = $vacancy->translates->where('language.is_default', true)->first() 
                                                        ?? $vacancy->translates->first();
                                                @endphp
                                                {{ $mainTranslation->position ?? 'N/A' }}
                                            </td>
                                            <td>
                                                @php
                                                    $mainTranslation = $vacancy->translates->where('language.is_default', true)->first() 
                                                        ?? $vacancy->translates->first();
                                                @endphp
                                                {{ $mainTranslation->experience ?? 'N/A' }}
                                            </td>
                                            <td>{{ $vacancy->address }}</td>
                                            <td>
                                                <span class="{{ $vacancy->is_expired ? 'text-danger' : 'text-success' }}">
                                                    {{ $vacancy->last_apply_date ? $vacancy->last_apply_date->format('M d, Y') : '—' }}

                                                </span>
                                            </td>
                                            <td>{{ $vacancy->email }}</td>
                                            <td>
                                                @if($vacancy->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.vacancy-requirements.create-for-vacancy', $vacancy->id) }}" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       title="Add Requirements">
                                                        <i class="material-icons-outlined">add</i>
                                                    </a>
                                                    <a href="{{ route('admin.vacancies.edit', $vacancy) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger delete-vacancy-btn" 
                                                            data-vacancy-id="{{ $vacancy->id }}"
                                                            data-vacancy-title="{{ $mainTranslation->title ?? 'N/A' }}"
                                                            data-delete-url="{{ route('admin.vacancies.destroy', $vacancy) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No vacancies found.</td>
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

<!-- Hidden form for delete -->
<form id="delete-vacancy-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('styles')
<style>
.requirements-count {
    background: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 10px;
    margin-left: 4px;
    min-width: 16px;
    display: inline-block;
    text-align: center;
}

.requirements-content {
    border-left: 4px solid #0d6efd;
}

.requirements-row {
    background-color: #f8f9fa;
}

.requirements-row td {
    padding: 0 !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle requirements functionality
    document.querySelectorAll('.toggle-requirements-btn').forEach(button => {
        button.addEventListener('click', function() {
            const vacancyId = this.getAttribute('data-vacancy-id');
            const requirementsRow = document.getElementById(`requirements-${vacancyId}`);
            
            if (requirementsRow.style.display === 'none') {
                requirementsRow.style.display = 'table-row';
                this.classList.add('btn-info');
                this.classList.remove('btn-outline-info');
            } else {
                requirementsRow.style.display = 'none';
                this.classList.remove('btn-info');
                this.classList.add('btn-outline-info');
            }
        });
    });

    // Delete requirement functionality
    document.querySelectorAll('.delete-requirement-btn').forEach(button => {
        button.addEventListener('click', function() {
            const requirementId = this.getAttribute('data-requirement-id');
            const requirementTitle = this.getAttribute('data-requirement-title');
            const deleteUrl = this.getAttribute('data-delete-url');
            
            if (confirm(`Are you sure you want to delete requirement "${requirementTitle}"?`)) {
                const form = document.getElementById('delete-vacancy-form');
                form.action = deleteUrl;
                form.submit();
            }
        });
    });

    const tbody = document.getElementById('sortable-vacancies');
    
    if (tbody) {
        new Sortable(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                const vacancies = rows.map((row, index) => ({
                    id: row.getAttribute('data-id'),
                    order: index + 1
                }));
                
                // Update order numbers in the table
                rows.forEach((row, index) => {
                    const orderCell = row.querySelector('td:first-child span');
                    if (orderCell) {
                        orderCell.textContent = index + 1;
                    }
                });
                
                // Send reorder request
                fetch('{{ route("admin.vacancies.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        vacancies: vacancies.map(v => v.id)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Reorder failed:', data.message);
                        alert('Error reordering vacancies: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Reorder error:', error);
                });
            }
        });
    }
    
    // Delete confirmation
    document.querySelectorAll('.delete-vacancy-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const vacancyId = this.getAttribute('data-vacancy-id');
            const vacancyTitle = this.getAttribute('data-vacancy-title');
            const deleteUrl = this.getAttribute('data-delete-url');
            
            if (confirm(`Do you want to delete "${vacancyTitle}"? This action cannot be undone.`)) {
                const form = document.getElementById('delete-vacancy-form');
                form.action = deleteUrl;
                form.submit();
            }
        });
    });
});
</script>
@endpush 