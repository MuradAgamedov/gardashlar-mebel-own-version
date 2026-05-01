@extends('admin.layout.app')

@section('title', 'Vacancy Requirements')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Vacancy Requirements</h3>
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
                        <li class="breadcrumb-item active">Vacancy Requirements</li>
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
                            <h5>All Vacancy Requirements</h5>
                            <div class="d-flex gap-2">
                                <!-- Filter by Vacancy -->
                                <select class="form-select" id="vacancyFilter" style="width: auto;">
                                    <option value="">All Vacancies</option>
                                    @foreach($vacancies as $vacancy)
                                        @php
                                            $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                            $vacancyTranslation = $vacancy->translates->firstWhere('language_id', $mainLanguageId)
                                                ?? $vacancy->translates->first();
                                        @endphp
                                        <option value="{{ $vacancy->id }}" {{ request('vacancy_id') == $vacancy->id ? 'selected' : '' }}>
                                            {{ $vacancyTranslation->title ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('vacancy_id'))
                                    <a href="{{ route('admin.vacancy-requirements.create-for-vacancy', request('vacancy_id')) }}" class="btn btn-primary" title="{{ __('Add New Requirement') }}">
                                        <i class="material-icons-outlined">add</i>
                                    </a>
                                @else
                                    <a href="{{ route('admin.vacancy-requirements.create') }}" class="btn btn-primary" title="{{ __('Add New Requirement') }}">
                                        <i class="material-icons-outlined">add</i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">Order</th>
                                        <th>Title</th>
                                        <th>Vacancy</th>
                                        <th>Status</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-requirements">
                                    @forelse($requirements as $requirement)
                                        <tr data-id="{{ $requirement->id }}">
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
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                    $mainTranslation = $requirement->translates->firstWhere('language_id', $mainLanguageId)
                                                        ?? $requirement->translates->first();
                                                @endphp
                                                <strong>{{ $mainTranslation->title ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                    $vacancyTranslation = $requirement->vacancy->translates->firstWhere('language_id', $mainLanguageId)
                                                        ?? $requirement->vacancy->translates->first();
                                                @endphp
                                                {{ $vacancyTranslation->title ?? 'N/A' }}
                                            </td>
                                            <td>
                                                @if($requirement->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.vacancy-requirements.edit', $requirement) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger delete-requirement-btn" 
                                                            data-requirement-id="{{ $requirement->id }}"
                                                            data-requirement-title="{{ $mainTranslation->title ?? 'N/A' }}"
                                                            data-delete-url="{{ route('admin.vacancy-requirements.destroy', $requirement) }}"
                                                            title="{{ __('Delete') }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No requirements found.</td>
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
<form id="delete-requirement-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('sortable-requirements');
    
    if (tbody) {
        new Sortable(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                const requirements = rows.map((row, index) => ({
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
                fetch('{{ route("admin.vacancy-requirements.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        requirements: requirements.map(r => r.id)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Order updated successfully - no alert needed
                    } else {
                        console.error('Reorder failed:', data.message);
                        alert('Error reordering requirements: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Reorder error:', error);
                    alert('Error reordering requirements. Please try again.');
                });
            }
        });
    }
    
    // Delete confirmation
    document.querySelectorAll('.delete-requirement-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const requirementId = this.getAttribute('data-requirement-id');
            const requirementTitle = this.getAttribute('data-requirement-title');
            const deleteUrl = this.getAttribute('data-delete-url');
            
            if (confirm(`Do you want to delete "${requirementTitle}"? This action cannot be undone.`)) {
                const form = document.getElementById('delete-requirement-form');
                form.action = deleteUrl;
                form.submit();
            }
        });
    });
    
    // Vacancy filter functionality
    const vacancyFilter = document.getElementById('vacancyFilter');
    if (vacancyFilter) {
        vacancyFilter.addEventListener('change', function() {
            const vacancyId = this.value;
            const currentUrl = new URL(window.location);
            
            if (vacancyId) {
                currentUrl.searchParams.set('vacancy_id', vacancyId);
            } else {
                currentUrl.searchParams.delete('vacancy_id');
            }
            
            window.location.href = currentUrl.toString();
        });
    }
});
</script>
@endpush 