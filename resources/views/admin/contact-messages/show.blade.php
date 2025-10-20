@extends('admin.layout.app')

@section('title', 'View Message')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>View Message</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.contact-messages.index') }}">Contact Messages</a>
                        </li>
                        <li class="breadcrumb-item active">View</li>
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
                                <h5>Message Details</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end gap-2">
                                    @if(!$message->is_read)
                                        <form method="POST" action="{{ route('admin.contact-messages.mark-as-read', $message->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="material-icons-outlined">mark_email_read</i> Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.contact-messages.destroy', $message->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this message?')">
                                            <i class="material-icons-outlined">delete</i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">
                                        <i class="material-icons-outlined">arrow_back</i> 
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Message Content -->
                                <div class="mb-4">
                                    <h6 class="text-muted mb-2">Message:</h6>
                                    <div style="color: black !important" class="">
                                        <p style="color: black" class="mb-0">{{ $message->message }}</p>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Name:</label>
                                            <p style="color: black !important" class="form-control-plaintext">{{ $message->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Email:</label>
                                            <p class="form-control-plaintext">
                                                <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">City:</label>
                                            <p class="form-control-plaintext">{{ $message->city }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Status:</label>
                                            <p class="form-control-plaintext">
                                                @if($message->is_read)
                                                    <span class="badge bg-success">Read</span>
                                                    @if($message->read_at)
                                                        <small class="text-muted d-block mt-1">
                                                            Read on: {{ $message->read_at->format('d.m.Y H:i') }}
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning">Unread</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Message Info -->
                                <div class="card border">
                                    <div class="card-header">
                                        <h6 class="mb-0">Message Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Sent:</label>
                                            <p class="form-control-plaintext">{{ $message->created_at->format('d.m.Y H:i') }}</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Message ID:</label>
                                            <p class="form-control-plaintext">#{{ $message->id }}</p>
                                        </div>

                                        @if($message->is_read && $message->read_at)
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Read:</label>
                                                <p class="form-control-plaintext">{{ $message->read_at->format('d.m.Y H:i') }}</p>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label text-muted">Time since sent:</label>
                                            <p class="form-control-plaintext">{{ $message->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 