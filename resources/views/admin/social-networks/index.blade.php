@extends('admin.layout.app')

@section('title', __('Social Networks'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Social Networks') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Social Networks') }}</li>
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

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ __('Social Networks') }}</h5>
                            <a href="{{ route('admin.social-networks.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">{{ __('Order') }}</th>
                                        <th style="width: 15%;">{{ __('Type') }}</th>
                                        <th style="width: 20%;">{{ __('Name') }}</th>
                                        <th style="width: 30%;">{{ __('URL') }}</th>
                                        <th style="width: 10%;">{{ __('Status') }}</th>
                                        <th style="width: 20%;">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable">
                                    @forelse($socialNetworks as $socialNetwork)
                                        <tr data-id="{{ $socialNetwork->id }}" class="sortable-item">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="cursor: move;">drag_handle</i>
                                                    <span class="badge bg-secondary">{{ $socialNetwork->order }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="{{ $socialNetwork->display_icon }}" 
                                                       style="color: {{ $socialNetwork->color }}; font-size: 20px; margin-right: 10px;"></i>
                                                    <div>
                                                        <strong class="text-dark">{{ ucfirst($socialNetwork->type) }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ __('ID') }}: {{ $socialNetwork->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $socialNetwork->display_name }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ $socialNetwork->url }}" 
                                                   target="_blank" 
                                                   class="text-decoration-none text-primary"
                                                   title="{{ $socialNetwork->url }}">
                                                    {{ Str::limit($socialNetwork->url, 40) }}
                                                    <i class="material-icons-outlined" style="font-size: 14px;">open_in_new</i>
                                                </a>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.social-networks.toggle-status', $socialNetwork->id) }}" 
                                                      method="POST" 
                                                      style="display: inline;">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm {{ $socialNetwork->status ? 'btn-success' : 'btn-secondary' }}"
                                                            title="{{ $socialNetwork->status ? __('Active') : __('Inactive') }}">
                                                        <i class="material-icons-outlined">{{ $socialNetwork->status ? 'check_circle' : 'cancel' }}</i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.social-networks.edit', $socialNetwork->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="{{ __('Edit') }}">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <form action="{{ route('admin.social-networks.destroy', $socialNetwork->id) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this social network?') }}')"
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="{{ __('Delete') }}">
                                                            <i class="material-icons-outlined">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="material-icons-outlined" style="font-size: 48px; color: #6c757d;">share</i>
                                                <h5 class="text-muted mt-3">{{ __('No Social Networks') }}</h5>
                                                <p class="text-muted">{{ __('Start by adding social networks.') }}</p>
                                                <a href="{{ route('admin.social-networks.create') }}" class="btn btn-primary">
                                                    <i class="material-icons-outlined">add</i>
                                                </a>
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

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortable = document.getElementById('sortable');
    if (sortable) {
        new Sortable(sortable, {
            animation: 150,
            handle: '.sortable-item',
            onEnd: function(evt) {
                const items = Array.from(sortable.querySelectorAll('.sortable-item')).map((item, index) => ({
                    id: item.dataset.id,
                    order: index
                }));

                fetch('{{ route("admin.social-networks.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ orders: items.map(item => item.id) })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }
});
</script>
@endsection