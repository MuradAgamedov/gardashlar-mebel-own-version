@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Users') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Registered Users') }}</li>
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
                            <h5>{{ __('Registered Users List') }}</h5>
                            <a href="{{ route('admin.registered-users.export') }}" class="btn btn-success">
                                <i class="material-icons-outlined">download</i>
                                {{ __('Excel Export') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Surname') }}</th>
                                            <th>{{ __('FIN') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Phone') }}</th>
                                            <th>{{ __('Created Date') }}</th>
                                            <th width="100">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $index => $user)
                                            <tr>
                                                <td>{{ $users->firstItem() + $index }}</td>
                                                <td>{{ $user->name ?: '-' }}</td>
                                                <td>{{ $user->surname ?: '-' }}</td>
                                                <td>{{ $user->fin ?: '-' }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->phone ?: '-' }}</td>
                                                <td>{{ $user->created_at?->format('d.m.Y H:i') ?: '-' }}</td>
                                                <td>
                                                    <form action="{{ route('admin.registered-users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}" onclick="return confirm('{{ __('Are you sure you want to delete this user?') }}');">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5v-7zM4.118 4 4 4.059V4h8v.059L11.882 4H4.118zM2.5 3h11a.5.5 0 0 1 0 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a.5.5 0 0 1 0-1z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $users->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="material-icons-outlined mb-3" style="font-size: 48px;">people</i>
                                <p>{{ __('No users found') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
