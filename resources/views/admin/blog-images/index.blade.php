@extends('admin.layout.app')

@section('title', 'Blog Images Management')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Blog Images Management</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.blog-posts.index') }}">Blog Posts</a>
                        </li>
                        <li class="breadcrumb-item active">Blog Images</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif


        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Blog Images - {{ $blogPost->translate()->title }}</h5>

                <div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                        <i class="material-icons-outlined">add_photo_alternate</i>
                    </button>

                    <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-secondary">
                        <i class="material-icons-outlined">arrow_back</i>
                    </a>
                </div>
            </div>

            <div class="card-body">
                @if($images->count() > 0)
                    <div class="row" id="imagesContainer">
                        @foreach($images as $image)
                            <div class="col-md-3 col-sm-6 mb-4" data-id="{{ $image->id }}">
                                <div class="card image-card">
                                    <img src="{{ asset('storage/'.$image->image_url) }}"
                                         class="img-fluid"
                                         style="height:200px;object-fit:cover;border-radius:5px">

                                    <div class="mt-2 d-flex justify-content-between">
                                        <small>Order: {{ $image->order }}</small>

                                        <div>
                                            <button class="btn btn-warning btn-sm"
                                                onclick="editImage({{ $image->id }}, '{{ asset('storage/'.$image->image_url) }}')">
                                                <i class="material-icons-outlined">edit</i>
                                            </button>

                                            <button class="btn btn-danger btn-sm delete-btn"
                                                data-url="{{ route('admin.blog-posts.images.destroy', [$blogPost->id, $image->id]) }}">
                                                <i class="material-icons-outlined">delete</i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted">No images found.</p>
                @endif
            </div>
        </div>
    </div>

</div>


<!-- Upload Modal -->
<div class="modal fade" id="uploadImageModal">
    <div class="modal-dialog">
        <form class="modal-content"
              method="POST"
              enctype="multipart/form-data"
              action="{{ route('admin.blog-posts.images.store', $blogPost->id) }}">
            @csrf

            <div class="modal-header">
                <h5>Upload Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="file"
                       class="form-control"
                       name="images[]"
                       id="images"
                       accept="image/*"
                       multiple
                       required>

                <small class="text-muted">Max 2MB each</small>

                <div id="imagePreviews" class="mt-2"></div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Upload</button>
            </div>

        </form>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editImageModal">
    <div class="modal-dialog">
        <form class="modal-content" id="editImageForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5>Edit Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="file"
                       class="form-control"
                       id="editImage"
                       name="image"
                       accept="image/*"
                       required>

                <img id="editImagePreview"
                     class="img-fluid mt-2"
                     style="display:none;max-height:200px;border-radius:5px">

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Save changes</button>
            </div>

        </form>
    </div>
</div>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>

new Sortable(imagesContainer, {
    animation:150,
    onEnd: function() {
        let data = [];

        [...imagesContainer.children].forEach((item, index) => {
            data.push({ id: item.dataset.id, order: index + 1 });
        });

        fetch("{{ route('admin.blog-posts.images.reorder', $blogPost->id) }}", {
            method: "POST",
            headers: {
                "Content-Type":"application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ images: data })
        });
    }
});


function editImage(id, url) {
    let form = document.getElementById("editImageForm");
    form.action = "{{ url('/admin/blog-posts/'.$blogPost->id.'/images') }}/" + id;

    document.getElementById("editImagePreview").src = url;
    document.getElementById("editImagePreview").style.display = "block";

    new bootstrap.Modal(document.getElementById('editImageModal')).show();
}


// Delete
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.onclick = function(){
        let url = this.dataset.url;

        if(confirm("Delete image?")){
            let f = document.createElement("form");
            f.method = "POST";
            f.action = url;
            f.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(f);
            f.submit();
        }
    }
});


</script>
@endpush
