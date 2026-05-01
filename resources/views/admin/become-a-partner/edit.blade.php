@extends('admin.layout.app')

@section('title', __('Become A Partner'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Become A Partner') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Become A Partner') }}</li>
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
                        <h5>{{ __('Edit Become A Partner Section') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.become-a-partner.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Multi-language Tabs -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $becomeAPartner->translates->where('language_id', $language->id)->first();
                                                $hasErrors = $errors->has("translations.{$language->id}.*");
                                                $hasTranslation = $translation !== null;
                                            @endphp
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link @if($index === 0) active @endif" 
                                                        id="tab-{{ $language->id }}" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#content-{{ $language->id }}" 
                                                        type="button" 
                                                        role="tab" 
                                                        aria-controls="content-{{ $language->id }}" 
                                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                                        style="@if($hasErrors) border-color: #dc3545; @elseif($hasTranslation) border-color: #198754; @else border-color: #ffc107; @endif">
                                                    {{ $language->lang_code }}
                                                    @if($hasErrors)
                                                        <i class="material-icons-outlined text-danger ms-1" style="font-size: 16px;">error</i>
                                                    @elseif($hasTranslation)
                                                        <i class="material-icons-outlined text-success ms-1" style="font-size: 16px;">check_circle</i>
                                                    @else
                                                        <i class="material-icons-outlined text-warning ms-1" style="font-size: 16px;">warning</i>
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content" id="languageTabContent">
                                        @foreach($languages as $index => $language)
                                            @php
                                                $translation = $becomeAPartner->translates->where('language_id', $language->id)->first();
                                            @endphp
                                            <div class="tab-pane fade @if($index === 0) show active @endif" 
                                                 id="content-{{ $language->id }}" 
                                                 role="tabpanel" 
                                                 aria-labelledby="tab-{{ $language->id }}">
                                                
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <label for="content_{{ $language->id }}" class="form-label">
                                                            {{ __('Content') }} <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control ckeditor @error("translations.{$language->id}.content") is-invalid @enderror" 
                                                                  id="content_{{ $language->id }}" 
                                                                  name="translations[{{ $language->id }}][content]" 
                                                                  placeholder="{{ __('Enter content for :lang', ['lang' => $language->lang_code]) }}">{{ old("translations.{$language->id}.content", $translation->content ?? '') }}</textarea>
                                                        @error("translations.{$language->id}.content")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                        </button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
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

        <!-- The Advantages To Become A Partner Section -->
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ __('The Advantages To Become A Partner') }}</h5>
                            <div class="d-flex gap-2">
                                <button type="button" id="deleteSelectedAdvantagesBtn" class="btn btn-danger" style="display: none;">
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                                <a href="{{ route('admin.become-a-partner-advantages.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllAdvantages" class="form-check-input">
                                        </th>
                                        <th width="50">{{ __('Order') }}</th>
                                        <th width="80">{{ __('Icon') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th width="80">{{ __('Status') }}</th>
                                        <th width="150">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-advantages">
                                    @php
                                        $advantages = \App\Models\BecomeAPartnerAdvantage::with('translates.language')->orderBy('order')->get();
                                    @endphp
                                    @forelse($advantages as $advantage)
                                        @php
                                            $firstTranslation = $advantage->translates->first();
                                        @endphp
                                        <tr data-id="{{ $advantage->id }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input advantage-checkbox" value="{{ $advantage->id }}">
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
                                                @if($advantage->icon_url)
                                                    <img src="{{ $advantage->icon_url }}" alt="{{ __('Icon') }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="material-icons-outlined text-muted">image</i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $firstTranslation ? $firstTranslation->title : __('No translation') }}
                                            </td>
                                          
                                            <td>
                                                @if($advantage->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.become-a-partner-advantages.edit', $advantage) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : __('Advantage :id', ['id' => $advantage->id]) }}"
                                                            data-url="{{ route('admin.become-a-partner-advantages.destroy', $advantage) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">trending_up</i>
                                                    <p class="mt-2">{{ __('No advantages found') }}</p>
                                                    <a href="{{ route('admin.become-a-partner-advantages.create') }}" class="btn btn-primary">
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

@push('styles')
<style>
.nav-tabs .nav-link {
    border: 1px solid #dee2e6;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
    color: #6c757d;
    background-color: #f8f9fa;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}

.drag-handle {
    cursor: grab;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
}

.drag-handle:active {
    cursor: grabbing;
}

.sortable-ghost {
    opacity: 0.5;
    background-color: #f8f9fa;
}

.sortable-chosen {
    background-color: #e3f2fd;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for each language tab
    @foreach($languages as $language)
        ClassicEditor
            .create(document.querySelector('#content_{{ $language->id }}'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                height: 400
            })
            .catch(error => {
                console.error(error);
            });
    @endforeach

    // Auto-switch to first tab with errors on form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const tabsWithErrors = document.querySelectorAll('.nav-link[style*="border-color: #dc3545"]');
        if (tabsWithErrors.length > 0) {
            e.preventDefault();
            const firstTabWithError = tabsWithErrors[0];
            const tab = new bootstrap.Tab(firstTabWithError);
            tab.show();
        }
    });

    // Drag and drop reordering for advantages
    const sortableList = document.getElementById('sortable-advantages');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const advantages = Array.from(sortableList.children).map((row, index) => {
                    return row.getAttribute('data-id');
                });

                fetch('{{ route("admin.become-a-partner-advantages.reorder") }}', {
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
                        Swal.fire({
                            title: '{{ __('Success!') }}',
                            text: '{{ __('Advantages reordered successfully!') }}',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: '{{ __('Error!') }}',
                            text: data.message || '{{ __('Failed to reorder advantages') }}',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }

    // Select All functionality for advantages
    const selectAllAdvantagesCheckbox = document.getElementById('selectAllAdvantages');
    const advantageCheckboxes = document.querySelectorAll('.advantage-checkbox');
    const deleteSelectedAdvantagesBtn = document.getElementById('deleteSelectedAdvantagesBtn');

    selectAllAdvantagesCheckbox.addEventListener('change', function() {
        advantageCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteSelectedAdvantagesButton();
    });

    advantageCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteSelectedAdvantagesButton();
            updateSelectAllAdvantages();
        });
    });

    function updateDeleteSelectedAdvantagesButton() {
        const checkedBoxes = document.querySelectorAll('.advantage-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deleteSelectedAdvantagesBtn.style.display = 'inline-flex';
        } else {
            deleteSelectedAdvantagesBtn.style.display = 'none';
        }
    }

    function updateSelectAllAdvantages() {
        const checkedBoxes = document.querySelectorAll('.advantage-checkbox:checked');
        const totalBoxes = advantageCheckboxes.length;
        selectAllAdvantagesCheckbox.checked = checkedBoxes.length === totalBoxes;
        selectAllAdvantagesCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes;
    }

    // Delete Selected Advantages functionality
    deleteSelectedAdvantagesBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.advantage-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        Swal.fire({
            title: '{{ __('Are you sure?') }}',
            text: '{{ __('Do you want to delete :count selected advantage(s)?', ['count' => ':count']) }}'.replace(':count', checkedBoxes.length),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __('Yes, delete them!') }}',
            cancelButtonText: '{{ __('Cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.become-a-partner-advantages.delete-selected") }}';
                form.innerHTML = `
                    @csrf
                    ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Delete confirmation for advantages
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');

            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('Do you want to delete :title?', ['title' => ':title']) }}'.replace(':title', title),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __('Yes, delete it!') }}',
                cancelButtonText: '{{ __('Cancel') }}'
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
@endpush 