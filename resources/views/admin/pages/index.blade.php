@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Pages Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Pages</li>
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
                            <h5>Pages List</h5>
                            {{-- <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card overflow-hidden">
                            <div class="card-header card-no-border">
                                <h3>Pages Table</h3>
                                <p class="desc mb-0 mt-1">Manage your application pages.</p>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr class="border-bottom-secondary border-top-0">
                                            {{-- <th scope="col">ID</th> --}}
                                            <th scope="col">Title</th>
                                            <th scope="col">Slug</th>
                                            <th scope="col">Keyword</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pages as $page)
                                        <tr class="border-bottom-{{ $loop->even ? 'info' : 'success' }}">
                                            {{-- <th scope="row">{{ $page->id }}</th> --}}
                                            <td>
                                                @if($page->translates->count() > 0)
                                                    @php
                                                        $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                        $mainTranslation = $page->translates->firstWhere('language_id', $mainLanguageId)
                                                            ?? $page->translates->first();
                                                    @endphp
                                                    {{ $mainTranslation->title }}
                                                @else
                                                    <span class="text-muted">No title</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($page->translates->count() > 0)
                                                    @php
                                                        $mainLanguageId = \App\Models\Language::getMainLanguage()->id;
                                                        $mainTranslation = $page->translates->firstWhere('language_id', $mainLanguageId)
                                                            ?? $page->translates->first();
                                                    @endphp
                                                    <span class="badge badge-light-primary">{{ $mainTranslation->slug }}</span>
                                                @else
                                                    <span class="text-muted">No slug</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($page->keyword)
                                                    <span class="badge badge-light-info">{{ $page->keyword }}</span>
                                                @else
                                                    <span class="text-muted">No keyword</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($page->status)
                                                    <span class="badge badge-light-success">Active</span>
                                                @else
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="material-icons-outlined">edit</i>
                                                    </a>
                                                    {{-- <button type="button" 
                                                            class="btn btn-sm btn-outline-danger delete-page-btn" 
                                                            data-page-id="{{ $page->id }}"
                                                            data-page-title="{{ $page->translates->first()->title ?? 'Page' }}"
                                                            data-delete-url="{{ route('admin.pages.destroy', $page) }}">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button> --}}
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No pages found</td>
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
</div>

{{-- <!-- Hidden form for delete -->
<form id="delete-page-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form> --}}

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up delete buttons...');
    
    // SweetAlert for delete confirmation
    // document.querySelectorAll('.delete-page-btn').forEach(function(button) {
    //     button.addEventListener('click', function() {
    //         const pageId = this.getAttribute('data-page-id');
    //         const pageTitle = this.getAttribute('data-page-title');
    //         const deleteUrl = this.getAttribute('data-delete-url');
            
    //         console.log('Delete button clicked for:', pageTitle);
            
    //         // Check if SweetAlert is available
    //         if (typeof Swal !== 'undefined') {
    //             Swal.fire({
    //                 title: 'Are you sure?',
    //                 text: `Do you want to delete "${pageTitle}"? This action cannot be undone.`,
    //                 icon: 'warning',
    //                 showCancelButton: true,
    //                 confirmButtonColor: '#d33',
    //                 cancelButtonColor: '#3085d6',
    //                 confirmButtonText: 'Yes, delete it!',
    //                 cancelButtonText: 'Cancel'
    //             }).then((result) => {
    //                 if (result.isConfirmed) {
    //                     console.log('SweetAlert confirmed, submitting form...');
    //                     submitDeleteForm(deleteUrl);
    //                 }
    //             });
    //         } else {
    //             // Fallback to basic confirm
    //             console.log('SweetAlert not available, using basic confirm...');
    //             if (confirm(`Do you want to delete "${pageTitle}"? This action cannot be undone.`)) {
    //                 submitDeleteForm(deleteUrl);
    //             }
    //         }
    //     });
    // });
    
    // function submitDeleteForm(deleteUrl) {
    //     const form = document.getElementById('delete-page-form');
    //     form.action = deleteUrl;
    //     form.submit();
    // }
});
</script>
@endpush 