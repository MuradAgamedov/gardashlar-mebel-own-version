@extends('admin.layouts.app')

@section('title', 'Company Actions & Products Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Actions & Products Management</h4>
                            <p class="text-muted mb-0">
                                <strong>{{ $company->translate()->first()->title ?? 'No Company' }}</strong>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.actions.create') }}?company_id={{ $company->id }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                                Yeni Action Yarat
                            </a>
                            <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
                                <i class="material-icons-outlined">arrow_back</i>
                                Geri
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Actions Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Actions ({{ $actions->count() }})</h5>
                            @if($actions->count() > 0)
                                <div class="row">
                                    @foreach($actions as $action)
                                        @php
                                            $mainTranslate = $action->translate()->first();
                                        @endphp
                                        <div class="col-md-4 col-lg-3 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-header bg-primary text-white">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">
                                                            @if($action->icon)
                                                                <i class="material-icons-outlined">{{ $action->icon }}</i>
                                                            @endif
                                                            {{ $mainTranslate->title ?? 'No Title' }}
                                                        </h6>
                                                        <span class="badge bg-light text-dark">
                                                            {{ $action->companyProducts->count() }} məhsul
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    @if($mainTranslate->description)
                                                        <p class="card-text small">{{ Str::limit($mainTranslate->description, 100) }}</p>
                                                    @endif
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            {{ $action->start_date ? $action->start_date->format('Y-m-d') : 'No start date' }} - 
                                                            {{ $action->end_date ? $action->end_date->format('Y-m-d') : 'No end date' }}
                                                        </small>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('admin.actions.edit', $action) }}" class="btn btn-outline-primary btn-sm">
                                                                <i class="material-icons-outlined">edit</i>
                                                            </a>
                                                            <a href="{{ route('admin.actions.products', $action) }}" class="btn btn-outline-success btn-sm">
                                                                <i class="material-icons-outlined">inventory</i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="material-icons-outlined text-muted" style="font-size: 3rem;">local_offer</i>
                                    <h6 class="text-muted mt-2">Bu company üçün action yoxdur</h6>
                                    <a href="{{ route('admin.actions.create') }}?company_id={{ $company->id }}" class="btn btn-primary btn-sm">
                                        <i class="material-icons-outlined">add</i>
                                        İlk Action-u Yarat
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Products Section -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Məhsullar ({{ $products->total() }})</h5>
                            
                            <!-- Search -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <form method="GET" action="{{ route('admin.companies.actions', $company) }}" class="d-flex">
                                        <input type="text" name="search" class="form-control me-2" 
                                               placeholder="Məhsul axtar..." 
                                               value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="material-icons-outlined">search</i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Products Grid -->
                            <div class="row">
                                @forelse($products as $product)
                                    @php
                                        $mainTranslate = $product->translate()->first();
                                        $isInAnyAction = $product->companyProducts->isNotEmpty();
                                    @endphp
                                    <div class="col-md-4 col-lg-3 mb-4">
                                        <div class="card h-100 {{ $isInAnyAction ? 'border-success' : '' }}">
                                            <div class="position-relative">
                                                @if($mainTranslate && $mainTranslate->image)
                                                    <img src="{{ asset('storage/' . $mainTranslate->image) }}" 
                                                         class="card-img-top" 
                                                         alt="{{ $mainTranslate->image_alt_text ?? 'Product Image' }}"
                                                         style="height: 200px; object-fit: cover;">
                                                @else
                                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                         style="height: 200px;">
                                                        <i class="material-icons-outlined text-muted" style="font-size: 3rem;">image</i>
                                                    </div>
                                                @endif
                                                
                                                <!-- Action Badge -->
                                                @if($isInAnyAction)
                                                    <div class="position-absolute top-0 end-0 m-2">
                                                        <span class="badge bg-success">
                                                            <i class="material-icons-outlined" style="font-size: 1rem;">local_offer</i>
                                                            Action-da
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="card-title">{{ $mainTranslate->title ?? 'No Title' }}</h6>
                                                <p class="card-text text-muted small flex-grow-1">
                                                    {{ Str::limit($mainTranslate->description ?? 'No description', 100) }}
                                                </p>
                                                
                                                <div class="mt-auto">
                                                    @if($actions->count() > 0)
                                                        <div class="mb-2">
                                                            <label class="form-label small">Action seçin:</label>
                                                            <select class="form-select form-select-sm" id="actionSelect{{ $product->id }}">
                                                                <option value="">Action seçin</option>
                                                                @foreach($actions as $action)
                                                                    @php
                                                                        $isInThisAction = $product->companyProducts->where('action_id', $action->id)->isNotEmpty();
                                                                    @endphp
                                                                    <option value="{{ $action->id }}" {{ $isInThisAction ? 'disabled' : '' }}>
                                                                        {{ $action->translate()->first()->title ?? 'No Title' }}
                                                                        @if($isInThisAction) (Artıq əlavə edilib) @endif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        
                                                        @if($isInAnyAction)
                                                            <button type="button" class="btn btn-danger btn-sm w-100" 
                                                                    onclick="removeFromAllActions({{ $product->id }})">
                                                                <i class="material-icons-outlined">remove</i>
                                                                Bütün Action-lardan Çıxar
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-success btn-sm w-100" 
                                                                    onclick="addToAction({{ $product->id }})">
                                                                <i class="material-icons-outlined">add</i>
                                                                Action-a Əlavə Et
                                                            </button>
                                                        @endif
                                                    @else
                                                        <div class="text-center text-muted">
                                                            <small>Əvvəlcə action yaradın</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="material-icons-outlined text-muted" style="font-size: 4rem;">inventory</i>
                                            <h5 class="text-muted mt-3">Məhsul tapılmadı</h5>
                                            <p class="text-muted">Axtarış meyarlarınızı dəyişdirin</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Emal edilir...</p>
            </div>
        </div>
    </div>
</div>

<script>
function addToAction(productId) {
    const actionId = document.getElementById('actionSelect' + productId).value;
    
    if (!actionId) {
        alert('Zəhmət olmasa action seçin');
        return;
    }
    
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    fetch('{{ route("admin.companies.add-product-to-action", $company) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            action_id: actionId
        })
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.hide();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Xəta: ' + data.message);
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        alert('Məhsul əlavə edilərkən xəta baş verdi');
    });
}

function removeFromAllActions(productId) {
    if (!confirm('Bu məhsulu bütün action-lardan çıxarmaq istədiyinizə əminsiniz?')) {
        return;
    }
    
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    // Get all action IDs for this product
    const actionIds = Array.from(document.querySelectorAll(`#actionSelect${productId} option:not([disabled])`))
        .map(option => option.value)
        .filter(id => id !== '');
    
    if (actionIds.length === 0) {
        loadingModal.hide();
        alert('Bu məhsul heç bir action-da yoxdur');
        return;
    }
    
    // Remove from each action
    Promise.all(actionIds.map(actionId => 
        fetch('{{ route("admin.companies.remove-product-from-action", $company) }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                action_id: actionId
            })
        })
    ))
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(results => {
        loadingModal.hide();
        
        if (results.every(r => r.success)) {
            location.reload();
        } else {
            alert('Bəzi məhsullar çıxarıla bilmədi');
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        alert('Məhsul çıxarılarkən xəta baş verdi');
    });
}
</script>
@endsection
