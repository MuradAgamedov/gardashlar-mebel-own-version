@extends('admin.layout.app')

@section('title', 'Create Vacancy Requirement')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Create Vacancy Requirement</h3>
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.vacancy-requirements.index') }}">Vacancy Requirements</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Create New Vacancy Requirement</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.vacancy-requirements.store') }}" method="POST">
                            @csrf
                            
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Basic Information</h6>
                                    <div class="row">
                                        @if(isset($vacancy))
                                            <!-- Specific vacancy is selected -->
                                            <input type="hidden" name="vacancy_id" value="{{ $vacancy->id }}">
                                        @else
                                            <!-- Show vacancy selection dropdown -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Vacancy <span class="text-danger">*</span></label>
                                                <select class="form-control @error('vacancy_id') is-invalid @enderror" name="vacancy_id">
                                                    <option value="">Select a vacancy</option>
                                                    @foreach($vacancies as $vacancy)
                                                        @php
                                                            $vacancyTranslation = $vacancy->translates->where('language.is_default', true)->first() 
                                                                ?? $vacancy->translates->first();
                                                        @endphp
                                                        <option value="{{ $vacancy->id }}" {{ old('vacancy_id') == $vacancy->id ? 'selected' : '' }}>
                                                            {{ $vacancyTranslation->title ?? 'N/A' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('vacancy_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if(isset($vacancy))
                            <!-- Existing Requirements Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Existing Requirements for {{ $vacancy->translates->where('language.is_default', true)->first()->title ?? 'N/A' }}</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($vacancy->requirements->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="50">Order</th>
                                                                <th>Title</th>
                                                                <th>Status</th>
                                                                <th width="150">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="sortable-requirements">
                                                            @foreach($vacancy->requirements->sortBy('order') as $requirement)
                                                                <tr data-id="{{ $requirement->id }}">
                                                                    <td class="drag-handle" style="cursor: move;">
                                                                        <div class="d-flex align-items-center">
                                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M8 6C8 7.10457 7.10457 8 6 8C4.89543 8 4 7.10457 4 6C4 4.89543 4.89543 4 6 4C7.10457 4 8 4.89543 8 6Z" fill="#6c757d"/>
                                                                                <path d="M8 12C8 13.1046 7.10457 14 6 14C4.89543 14 4 13.1046 4 12C4 10.8954 4.89543 10 6 10C7.10457 10 8 10.8954 8 12Z" fill="#6c757d"/>
                                                                                <path d="M8 18C8 19.1046 7.10457 20 6 20C4.89543 20 4 19.1046 4 18C4 16.8954 4.89543 16 6 16C7.10457 16 8 16.8954 8 18Z" fill="#6c757d"/>
                                                                                <path d="M20 6C20 7.10457 19.1046 8 18 8C16.8954 8 16 7.10457 16 6C16 4.89543 16.8954 4 18 4C19.1046 4 20 4.89543 20 6Z" fill="#6c757d"/>
                                                                                <path d="M20 12C20 13.1046 19.1046 14 18 14C16.8954 14 16 13.1046 16 12C16 10.8954 16.8954 10 18 10C19.1046 10 20 10.8954 20 12Z" fill="#6c757d"/>
                                                                                <path d="M20 18C20 19.1046 19.1046 20 18 20C16.8954 20 16 19.1046 16 18C16 16.8954 16.8954 16 18 16C19.1046 16 20 16.8954 20 18Z" fill="#6c757d"/>
                                                                            </svg>
                                                                            <span class="ms-1">{{ $requirement->order }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            $reqTranslation = $requirement->translates->where('language.is_default', true)->first() 
                                                                                ?? $requirement->translates->first();
                                                                        @endphp
                                                                        {{ $reqTranslation->title ?? 'N/A' }}
                                                                    </td>
                                                                    <td>
                                                                        @if($requirement->status)
                                                                            <span class="badge bg-success">Active</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">Inactive</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex gap-1">
                                                                            <a href="{{ route('admin.vacancy-requirements.edit', $requirement) }}" 
                                                                               class="btn btn-sm btn-outline-primary">
                                                                                <i class="material-icons-outlined">edit</i>
                                                                            </a>
                                                                            <button type="button" 
                                                                                    class="btn btn-sm btn-outline-danger delete-requirement-btn" 
                                                                                    data-requirement-id="{{ $requirement->id }}"
                                                                                    data-requirement-title="{{ $reqTranslation->title ?? 'N/A' }}"
                                                                                    data-delete-url="{{ route('admin.vacancy-requirements.destroy', $requirement) }}">
                                                                                <i class="material-icons-outlined">delete</i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-center text-muted py-3">
                                                    <i class="material-icons-outlined" style="font-size: 48px; opacity: 0.3;">list</i>
                                                    <p class="mt-2">No requirements found for this vacancy.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Content Translations Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Content Translations</h6>
                                    
                                    <!-- Language Tabs -->
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab" 
                                                        aria-controls="content-{{ $language->id }}" 
                                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                                    {{ $language->lang_code }}
                                                    @php
                                                        $hasErrors = $errors->has("translations.{$language->id}.*");
                                                    @endphp
                                                    @if($hasErrors)
                                                        <i class="fa fa-exclamation-triangle text-danger ms-1"></i>
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="tab-{{ $language->id }}">
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               class="form-control @error("translations.{$language->id}.title") is-invalid @enderror" 
                                                               name="translations[{{ $language->id }}][title]" 
                                                               value="{{ old("translations.{$language->id}.title") }}"
                                                               placeholder="Enter title for {{ $language->lang_code }}">
                                                        @error("translations.{$language->id}.title")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                                        <textarea class="form-control ckeditor @error("translations.{$language->id}.description") is-invalid @enderror" 
                                                                  name="translations[{{ $language->id }}][description]" 
                                                                  id="description-{{ $language->id }}"
                                                                  placeholder="Enter description for {{ $language->lang_code }}">{{ old("translations.{$language->id}.description") }}</textarea>
                                                        @error("translations.{$language->id}.description")
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            Active Status
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.vacancy-requirements.index') }}" class="btn btn-secondary">
                                            <i class="material-icons-outlined">cancel</i>
                                        </a>
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
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for each language tab
    @foreach($languages as $language)
        ClassicEditor
            .create(document.querySelector('#description-{{ $language->id }}'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                height: 300
            })
            .catch(error => {
                console.error(error);
            });
    @endforeach

    // Auto-switch to first tab with errors on form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const tabsWithErrors = document.querySelectorAll('.nav-link .fa-exclamation-triangle.text-danger');
        if (tabsWithErrors.length > 0) {
            e.preventDefault();
            const firstErrorTab = tabsWithErrors[0].closest('.nav-link');
            const tab = new bootstrap.Tab(firstErrorTab);
            tab.show();
        }
    });

    // Delete requirement functionality
    document.querySelectorAll('.delete-requirement-btn').forEach(button => {
        button.addEventListener('click', function() {
            const requirementId = this.getAttribute('data-requirement-id');
            const requirementTitle = this.getAttribute('data-requirement-title');
            const deleteUrl = this.getAttribute('data-delete-url');
            
            if (confirm(`Are you sure you want to delete requirement "${requirementTitle}"?`)) {
                // Create a hidden form for delete
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Initialize Sortable for requirements table
    const requirementsTbody = document.getElementById('sortable-requirements');
    if (requirementsTbody) {
        new Sortable(requirementsTbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const rows = requirementsTbody.querySelectorAll('tr[data-id]');
                const orderData = [];
                
                rows.forEach((row, index) => {
                    const requirementId = row.getAttribute('data-id');
                    orderData.push({
                        id: requirementId,
                        order: index + 1
                    });
                    
                    // Update order number in the table
                    const orderCell = row.querySelector('.drag-handle span');
                    if (orderCell) {
                        orderCell.textContent = index + 1;
                    }
                });
                
                // Send reorder request
                fetch('{{ route("admin.vacancy-requirements.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ requirements: orderData })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Reorder failed:', data.message);
                        alert('Error reordering requirements: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error reordering requirements. Please try again.');
                });
            }
        });
    }
});
</script>

<style>
.invalid-feedback.d-block {
    display: block !important;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.ck-editor__editable {
    min-height: 200px;
}
</style>
@endpush 