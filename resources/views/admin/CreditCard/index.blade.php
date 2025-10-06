@extends('admin.layout.app')

@section('title', 'Credit Cards')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Credit Cards</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Credit Cards</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">Success:</h6>
                <p class="mb-0">{{ session('success') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5>Credit Cards</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.credit-cards.create') }}" class="btn btn-primary">
                                        <i class="material-icons-outlined">add</i> 
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($creditCards->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>Title</th>
                                            <th>Image</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable">
                                        @foreach($creditCards as $card)
                                            <tr data-id="{{ $card->id }}">
                                                <td class="handle" style="cursor:move;">
                                                    <i class="material-icons-outlined">drag_indicator</i>
                                                </td>
                                                <td>{{ $card->title }}</td>
                                                <td>
                                                    @if($card->image)
                                                        <img src="{{ asset('storage/'.$card->image) }}" alt="Card Image" style="max-height: 50px;">
                                                    @else
                                                        <span class="text-muted">No image</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.credit-cards.edit', $card->id) }}" 
                                                           class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="material-icons-outlined">edit</i>
                                                        </a>
                                                        <form action="{{ route('admin.credit-cards.destroy', $card->id) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this card?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                <i class="material-icons-outlined">delete</i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center my-4">
                                Credit Cards list will appear here after you add some.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SortableJS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let el = document.getElementById('sortable');
        new Sortable(el, {
            handle: '.handle',
            animation: 150,
            onEnd: function () {
                let order = [];
                document.querySelectorAll('#sortable tr').forEach((row, index) => {
                    order.push(row.dataset.id);
                });

                fetch('{{ route("admin.credit-cards.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ credit_cards: order })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert('Error saving new order!');
                    }
                })
                .catch(err => console.error(err));
            }
        });
    });
</script>
@endsection
