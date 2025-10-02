@extends('admin.layout.app')

@section('title', 'RoluteeteActions')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>RoluteeteActions</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">RoluteeteActions</li>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Actions</h5>
                            <a href="{{ route('admin.roluteete-actions.create') }}" class="btn btn-primary">
                                <i class="material-icons-outlined">add</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($actions as $action)
                                    <tr>
                                        <td>{{ $action->id }}</td>
                                        <td>
                                            @if($action->image)
                                                <img src="{{ asset('storage/' . $action->image) }}" 
                                                     alt="Image" style="max-width: 100px; border-radius:5px;">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional($action->translates->first())->title ?? '---' }}
                                        </td>

                                        {{-- 👁 Status --}}
                                        <td class="text-center">
                                            <form action="{{ route('admin.roluteete-actions.toggleStatus', $action->id) }}" 
                                                  method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-light border-0">
                                                    @if($action->status)
                                                        <i class="material-icons-outlined text-success">visibility</i>
                                                    @else
                                                        <i class="material-icons-outlined text-danger">visibility_off</i>
                                                    @endif
                                                </button>
                                            </form>
                                        </td>


                                        {{-- Actions --}}
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- Edit --}}
                                                <a href="{{ route('admin.roluteete-actions.edit', $action->id) }}" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="material-icons-outlined">edit</i>
                                                </a>

                                                {{-- Delete --}}
                                                <form action="{{ route('admin.roluteete-actions.destroy', $action->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Silmək istədiyinizə əminsiniz?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No actions found</td>
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
@endsection
