@extends('admin.layout.app')

@section('title', 'Service Advantages')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Service Advantages</h3>
                    <p class="text-muted">{{ $service->translate()->title ?? 'Service' }}</p>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.services.index') }}">Services</a>
                        </li>
                        <li class="breadcrumb-item active">Advantages</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Create Form -->
        <div class="row mb-4">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Add New Advantage</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.service-advantages.store', $service->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Icon</label>
                                    <input type="file" 
                                           class="form-control @error('icon') is-invalid @enderror" 
                                           name="icon" 
                                           id="iconInput"
                                           accept="image/*">
                                    @error('icon')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div id="iconPreview" class="mt-2" style="display: none;">
                                        <img id="previewImage" src="" alt="Icon Preview" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; border-radius: 4px;">
                                        <small class="text-muted d-block">New icon preview</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Translations -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Translations</h6>
                                    <div class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                    id="tab-{{ $language->id }}" 
                                                    data-bs-toggle="tab" 
                                                    data-bs-target="#content-{{ $language->id }}" 
                                                    type="button" 
                                                    role="tab">
                                                {{ $language->title }}
                                            </button>
                                        @endforeach
                                    </div>
                                    
                                    <div class="tab-content mt-3" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel">
                                                <div class="row">
                                                    <input type="hidden" name="translates[{{ $index }}][language_id]" value="{{ $language->id }}" required>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Title *</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.title') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][title]" 
                                                               value="{{ old('translates.'.$index.'.title') }}"
                                                               placeholder="Enter title"
                                                               required>
                                                        @error('translates.'.$index.'.title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Icon Alt Text</label>
                                                        <input type="text" 
                                                               class="form-control @error('translates.'.$index.'.icon_alt_text') is-invalid @enderror" 
                                                               name="translates[{{ $index }}][icon_alt_text]" 
                                                               value="{{ old('translates.'.$index.'.icon_alt_text') }}"
                                                               placeholder="Enter icon alt text">
                                                        @error('translates.'.$index.'.icon_alt_text')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">Description *</label>
                                                        <textarea class="form-control @error('translates.'.$index.'.description') is-invalid @enderror" 
                                                                  name="translates[{{ $index }}][description]" 
                                                                  rows="3" 
                                                                  placeholder="Enter description"
                                                                  required>{{ old('translates.'.$index.'.description') }}</textarea>
                                                        @error('translates.'.$index.'.description')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons-outlined">save</i>
                                        Create Advantage
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Existing Advantages -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Existing Advantages</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Icon</th>
                                        <th>Status</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-advantages">
                                    @forelse($advantages as $advantage)
                                        <tr data-id="{{ $advantage->id }}">
                                            <td style="display: none;">
                                                <i class="fas fa-grip-vertical drag-handle" style="cursor: move;"></i>
                                                {{ $advantage->order }}
                                            </td>
                                            <td>
                                                @if($advantage->icon_url)
                                                    <img src="{{ $advantage->icon_url }}" alt="Icon" style="max-width: 50px; max-height: 50px;">
                                                @else
                                                    <span class="text-muted">No icon</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input status-toggle" type="checkbox" 
                                                           data-advantage-id="{{ $advantage->id }}"
                                                           {{ $advantage->status ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $mainTranslate = $advantage->translate();
                                                @endphp
                                                {{ $mainTranslate ? $mainTranslate->title : 'No title' }}
                                            </td>
                                            <td>
                                                @if($mainTranslate)
                                                    {{ Str::limit($mainTranslate->description, 100) }}
                                                @else
                                                    No description
                                                @endif
                                            </td>

                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.service-advantages.edit', [$service->id, $advantage->id]) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('admin.service-advantages.destroy', [$service->id, $advantage->id]) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this advantage?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No advantages found.</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Icon preview functionality
    const iconInput = document.getElementById('iconInput');
    const iconPreview = document.getElementById('iconPreview');
    const previewImage = document.getElementById('previewImage');
    
    if (iconInput) {
        iconInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    iconPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                iconPreview.style.display = 'none';
            }
        });
    }
    
    // Show validation errors on page load if they exist
    const invalidInputs = document.querySelectorAll('.is-invalid');
    invalidInputs.forEach(function(input) {
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.style.display = 'block';
        }
    });
    
    // Also check for any error messages in the page
    const errorMessages = document.querySelectorAll('.alert-danger');
    if (errorMessages.length > 0) {
        console.log('Found error messages:', errorMessages);
    }
    
    // Form validation - show errors when form is submitted
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            
            // Clear previous error states
            document.querySelectorAll('.is-invalid').forEach(function(input) {
                input.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
                feedback.style.display = 'none';
            });
            
            // Validate translations
            const languageTabs = document.querySelectorAll('.tab-pane');
            languageTabs.forEach(function(tab, index) {
                const titleInput = tab.querySelector('input[name*="[title]"]');
                const descriptionInput = tab.querySelector('textarea[name*="[description]"]');
                
                if (titleInput && !titleInput.value.trim()) {
                    titleInput.classList.add('is-invalid');
                    const feedback = titleInput.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Title is required.';
                        feedback.style.display = 'block';
                    }
                    hasErrors = true;
                }
                
                if (descriptionInput && !descriptionInput.value.trim()) {
                    descriptionInput.classList.add('is-invalid');
                    const feedback = descriptionInput.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Description is required.';
                        feedback.style.display = 'block';
                    }
                    hasErrors = true;
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                // Show the first tab with errors
                const firstInvalidTab = document.querySelector('.tab-pane .is-invalid').closest('.tab-pane');
                if (firstInvalidTab) {
                    const tabId = firstInvalidTab.id;
                    const tabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
                    if (tabButton) {
                        tabButton.click();
                    }
                }
            }
        });
    }
    const tbody = document.getElementById('sortable-advantages');
    
    if (tbody) {
        new Sortable(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                const advantages = rows.map((row, index) => ({
                    id: row.getAttribute('data-id'),
                    order: index + 1
                }));
                
                fetch('{{ route("admin.service-advantages.reorder", $service->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ advantages: advantages })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update order numbers in the table
                        rows.forEach((row, index) => {
                            const orderCell = row.querySelector('td:first-child');
                            const orderText = orderCell.textContent.replace(/\d+$/, (index + 1).toString());
                            orderCell.innerHTML = orderCell.innerHTML.replace(/\d+$/, (index + 1).toString());
                        });
                    }
                })
                .catch(error => {
                    console.error('Error updating order:', error);
                });
            }
        });
    }

    // Status toggle functionality
    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const advantageId = this.getAttribute('data-advantage-id');
            const status = this.checked;
            
            fetch(`/admin/services/{{ $service->id }}/advantages/${advantageId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert the toggle if failed
                    this.checked = !status;
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                // Revert the toggle if failed
                this.checked = !status;
                console.error('Error updating status:', error);
                alert('Error updating status');
            });
        });
    });
});
</script>
@endpush
@endsection 