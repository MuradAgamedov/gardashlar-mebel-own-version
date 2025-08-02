@extends('admin.layout.app')

@section('title', 'Blog Posts Management')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Blog Posts Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Blog Posts Management</li>
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
                            <h5>Blog Posts</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-danger" id="deleteSelectedBtn" style="display: none;">
                                    <i class="material-icons-outlined">delete</i>
                                </button>
                                <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">
                                    <i class="material-icons-outlined">add</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                                         <!-- Search and Filter Section -->
                     <div class="card-body border-bottom">
                         <form method="GET" action="{{ route('admin.blog-posts.index') }}" class="row g-3">
                             <div class="col-md-2">
                                 <label for="category_id" class="form-label">Filter by Category</label>
                                 <select class="form-select" id="category_id" name="category_id">
                                     <option value="">All Categories</option>
                                     @foreach($categories as $category)
                                         @php
                                             $categoryTranslation = $category->translates->first();
                                         @endphp
                                         <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                             {{ $categoryTranslation ? $categoryTranslation->title : 'No title' }}
                                         </option>
                                     @endforeach
                                 </select>
                             </div>
                             <div class="col-md-2">
                                 <label for="status" class="form-label">Filter by Status</label>
                                 <select class="form-select" id="status" name="status">
                                     <option value="">All Status</option>
                                     <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                     <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                 </select>
                             </div>
                             <div class="col-md-2">
                                 <label for="searchInput" class="form-label">Search Blog Posts</label>
                                 <div class="input-group">
                                     <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search blog posts..." value="{{ request('search') }}">
                                     <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                         <i class="material-icons-outlined">search</i>
                                     </button>
                                 </div>
                             </div>
                             <div class="col-md-3 d-flex align-items-end">
                                 <button type="submit" class="btn btn-outline-primary me-2">
                                     <i class="material-icons-outlined">filter_list</i>
                                 </button>
                                 @if(request('category_id') || request('status') || request('search'))
                                     <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline-secondary">
                                         <i class="material-icons-outlined">clear</i>
                                     </a>
                                 @endif
                             </div>
                             <div class="col-md-3 d-flex align-items-end justify-content-end">
                                 <div class="form-check">
                                     <input class="form-check-input" type="checkbox" id="selectAll">
                                     <label class="form-check-label" for="selectAll">
                                         Select All
                                     </label>
                                 </div>
                             </div>
                         </form>
                     </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input" id="headerCheckbox">
                                        </th>
                                        <th width="80">Image</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Published At</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($blogPosts as $blogPost)
                                        <tr class="blog-post-row">
                                            <td>
                                                <input type="checkbox" class="form-check-input blog-post-checkbox" value="{{ $blogPost->id }}">
                                            </td>
                                            <td>
                                                @if($blogPost->card_image)
                                                    <img src="{{ asset('storage/' . $blogPost->card_image) }}" 
                                                         alt="Blog Post Image" 
                                                         class="img-thumbnail" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="material-icons-outlined text-muted">image</i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="material-icons-outlined text-muted me-2" style="font-size: 16px;">article</i>
                                                    <div>
                                                        @php
                                                            $firstTranslation = $blogPost->translates->first();
                                                        @endphp
                                                        <strong>{{ $firstTranslation ? $firstTranslation->title : 'No title' }}</strong>
                                                        @if($firstTranslation && $firstTranslation->card_description)
                                                            <br><small class="text-muted">{{ Str::limit($firstTranslation->card_description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($blogPost->category)
                                                    @php
                                                        $categoryTranslation = $blogPost->category->translates->first();
                                                    @endphp
                                                    <span class="badge bg-secondary">
                                                        {{ $categoryTranslation ? $categoryTranslation->title : 'No category' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No category</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($blogPost->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($blogPost->published_at)
                                                    <small>{{ $blogPost->published_at->format('Y-m-d H:i') }}</small>
                                                @else
                                                    <span class="text-muted">Not published</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.blog-posts.edit', $blogPost) }}" class="btn btn-sm btn-warning">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                            data-title="{{ $firstTranslation ? $firstTranslation->title : 'Blog Post #' . $blogPost->id }}"
                                                            data-url="{{ route('admin.blog-posts.destroy', $blogPost) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="material-icons-outlined" style="font-size: 48px;">article</i>
                                                    <p class="mt-2">No blog posts found</p>
                                                    <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">
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
    .img-thumbnail {
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
         // Search functionality - now handled by backend
     const searchInput = document.getElementById('searchInput');
     const searchBtn = document.getElementById('searchBtn');

     searchBtn.addEventListener('click', function() {
         // Submit the form to trigger backend search
         searchInput.closest('form').submit();
     });
     
     searchInput.addEventListener('keyup', function(e) {
         if (e.key === 'Enter') {
             // Submit the form to trigger backend search
             this.closest('form').submit();
         }
     });

    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const headerCheckbox = document.getElementById('headerCheckbox');
    const blogPostCheckboxes = document.querySelectorAll('.blog-post-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

    function updateDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.blog-post-checkbox:checked');
        if (checkedBoxes.length > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.blog-post-checkbox:checked');
        const totalBoxes = blogPostCheckboxes.length;
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            headerCheckbox.checked = false;
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.checked = true;
            headerCheckbox.checked = true;
        } else {
            selectAllCheckbox.checked = false;
            headerCheckbox.checked = false;
        }
    }

    selectAllCheckbox.addEventListener('change', function() {
        blogPostCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
        updateSelectAllState();
    });

    headerCheckbox.addEventListener('change', function() {
        blogPostCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
        updateSelectAllState();
    });

    blogPostCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButton();
            updateSelectAllState();
        });
    });

    // Delete selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.blog-post-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) {
            Swal.fire({
                title: 'Warning!',
                text: 'Please select at least one blog post to delete.',
                icon: 'warning'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to delete ${selectedIds.length} selected blog post(s)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.blog-posts.bulk-delete") }}';
                form.innerHTML = `
                    @csrf
                    ${selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Delete confirmation for all delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
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