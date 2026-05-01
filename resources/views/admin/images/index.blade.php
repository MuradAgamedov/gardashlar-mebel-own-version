@extends('admin.layout.app')

@section('title', 'Images Management')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Images Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Images Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Images</h5>
                            <a href="{{ route('admin.images.create') }}" class="btn btn-primary" title="{{ __('Add Image') }}">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Image</th>
                                        <th>Keyword</th>
                                        <th>Alt Text</th>
                                        <th>Languages</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($images as $image)
                                        <tr>
                                            <td>{{ $image->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset('uploads/' . $image->image) }}" 
                                                         alt="{{ $image->translate()?->image_alt_text ?? 'Image' }}"
                                                         class="img-fluid rounded" 
                                                         style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">image</i>
                                                    <div>
                                                        <strong>{{ $image->keyword }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted">
                                                    {{ $image->translate()?->image_alt_text ?? 'No alt text' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @foreach($image->translates as $translate)
                                                        <span class="badge bg-primary">{{ $translate->language->lang_code }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.images.edit', $image) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $image->keyword }}"
                                                            data-url="{{ route('admin.images.destroy', $image) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">image</i>
                                                    <p class="mt-2">No images found</p>
                                                    <a href="{{ route('admin.images.create') }}" class="btn btn-primary" title="{{ __('Add First Image') }}">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const url = this.getAttribute('data-url');

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${title}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
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