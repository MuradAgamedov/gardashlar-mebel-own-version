@extends('admin.layout.app')

@section('title', __('Address Management'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Address Management') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Address Management') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            </div>
        @endif

        <!-- Address Types Section -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ __('Address Types') }}</h5>
                            <a href="{{ route('admin.address-types.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sortable-address-types" class="sortable-container">
                            @forelse($addressTypes as $addressType)
                                @php
                                    $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                    $firstTranslation = $addressType->translates->firstWhere('language_id', $mainLanguageId) ?? $addressType->translates->first();
                                    $typeAddresses = $addressType->addresses;
                                @endphp
                                
                                <!-- Address Type Card -->
                                <div class="address-type-card" data-id="{{ $addressType->id }}">
                                    <div class="card mb-3 address-type-indent">
                                        <div class="card-header d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="drag-handle me-3">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="8" cy="8" r="1"></circle>
                                                        <circle cx="16" cy="8" r="1"></circle>
                                                        <circle cx="8" cy="16" r="1"></circle>
                                                        <circle cx="16" cy="16" r="1"></circle>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : __('No translation') }}</strong>
                                                    </h6>
                                                    <div class="d-flex gap-1 mt-1">
                                                        @foreach($addressType->translates as $translate)
                                                            <span class="badge bg-primary">{{ $translate->language->lang_code }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($addressType->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.address-types.edit', $addressType) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <a href="{{ route('admin.address-types.addresses.create', $addressType->id) }}" class="btn btn-sm btn-success">
                                                        <i class="material-icons-outlined">add</i> 
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : __('Address Type #') . $addressType->id }}"
                                                            data-url="{{ route('admin.address-types.destroy', $addressType) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Addresses Container -->
                                        <div class="card-body p-0">
                                            <div class="addresses-container" data-parent="{{ $addressType->id }}">
                                                @if($typeAddresses->count() > 0)
                                                    @foreach($typeAddresses as $address)
                                                        <div class="address-item" data-address-id="{{ $address->id }}">
                                                            <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="drag-handle-address me-2">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                            <circle cx="8" cy="8" r="1"></circle>
                                                                            <circle cx="16" cy="8" r="1"></circle>
                                                                            <circle cx="8" cy="16" r="1"></circle>
                                                                            <circle cx="16" cy="16" r="1"></circle>
                                                                        </svg>
                                                                    </div>
                                                                    <div>
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="material-icons-outlined text-muted me-1" style="font-size: 14px;">location_on</i>
                                                                            @php
                                                                                $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                                                $mainAddressTranslate = $address->translates->firstWhere('language_id', $mainLanguageId)
                                                                                    ?? $address->translates->first();
                                                                            @endphp
                                                                            <div class="small">
                                                                                @if($mainAddressTranslate)
                                                                                    <div class="d-flex align-items-center">
                                                                                        <span class="badge bg-light text-dark me-1" style="font-size: 8px;">{{ $mainAddressTranslate->language->lang_code }}</span>
                                                                                        <span class="text-truncate" style="max-width: 200px;">{{ $mainAddressTranslate->address }}</span>
                                                                                    </div>
                                                                                @else
                                                                                    <span class="text-muted">{{ __('No translation') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <span class="badge bg-info mt-1" style="font-size: 10px;">{{ __('Address') }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    @if($address->status)
                                                                        <span class="badge bg-success" style="font-size: 10px;">{{ __('Active') }}</span>
                                                                    @else
                                                                        <span class="badge bg-secondary" style="font-size: 10px;">{{ __('Inactive') }}</span>
                                                                    @endif
                                                                    <div class="d-flex gap-1">
                                                                        <a href="{{ route('admin.addresses.edit', $address) }}" class="btn btn-sm btn-warning" style="padding: 2px 6px;">
                                                                            <i class="material-icons-outlined" style="font-size: 14px;">edit</i>
                                                                        </a>
                                                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                                                style="padding: 2px 6px;"
                                                                                data-title="{{ __('Address #') . $address->id }}"
                                                                                data-url="{{ route('admin.addresses.destroy', $address) }}">
                                                                            <i class="material-icons-outlined" style="font-size: 14px;">delete</i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="p-2 text-muted">
                                                        <i class="material-icons-outlined me-1" style="font-size: 14px;">info</i>
                                                        <small>{{ __('No addresses assigned to this type') }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="material-icons-outlined" style="font-size: 48px;">location_on</i>
                                        <p class="mt-2">{{ __('No address types found') }}</p>
                                        <a href="{{ route('admin.address-types.create') }}" class="btn btn-primary">
                                            <i class="material-icons-outlined">add</i>
                                        </a>
                                    </div>
                                </div>
                            @endforelse
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
    .drag-handle {
        cursor: grab;
        color: #6c757d;
        transition: color 0.2s;
        padding: 5px;
        border-radius: 4px;
    }
    .drag-handle:hover {
        color: #495057;
        background-color: #f8f9fa;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .drag-handle-address {
        cursor: grab;
        color: #6c757d;
        transition: color 0.2s;
        padding: 3px;
        border-radius: 3px;
    }
    .drag-handle-address:hover {
        color: #495057;
        background-color: #f8f9fa;
    }
    .drag-handle-address:active {
        cursor: grabbing;
    }
    .sortable-ghost {
        opacity: 0.5;
    }
    .sortable-chosen {
        background-color: #f8f9fa;
    }
    .address-type-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .address-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .address-type-indent {
        margin-left: 30px;
        padding-left: 20px;
        border-left: 3px solid #28a745;
        background-color: #f8f9fa;
    }
    .address-item {
        transition: background-color 0.2s;
    }
    .address-item:hover {
        background-color: #e9ecef;
    }
    .addresses-container {
        min-height: 20px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Drag and drop reordering for address types
    const sortableList = document.getElementById('sortable-address-types');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const addressTypes = Array.from(sortableList.querySelectorAll('.address-type-card')).map((card, index) => {
                    return card.getAttribute('data-id');
                });

                fetch('{{ route("admin.address-types.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ items: addressTypes })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire({
                            title: '{{ __("Error!") }}',
                            text: data.message || '{{ __("Failed to reorder address types") }}',
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

    // Drag and drop reordering for addresses within each type
    document.querySelectorAll('.addresses-container').forEach(container => {
        const addressItems = container.querySelectorAll('.address-item');
        if (addressItems.length > 1) {
            new Sortable(container, {
                handle: '.drag-handle-address',
                animation: 150,
                onEnd: function(evt) {
                    const addressIds = Array.from(container.querySelectorAll('.address-item')).map(item => {
                        return item.getAttribute('data-address-id');
                    });

                    fetch('{{ route("admin.addresses.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ items: addressIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            Swal.fire({
                                title: '{{ __("Error!") }}',
                                text: data.message || '{{ __("Failed to reorder addresses") }}',
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
    });

    // Delete confirmation for all delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');

            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("Do you want to delete \":title\"?", ["title" => ":title"]) }}'.replace(':title', title),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("Yes, delete it!") }}',
                cancelButtonText: '{{ __("Cancel") }}'
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