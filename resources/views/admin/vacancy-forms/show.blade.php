@extends('admin.layout.app')

@section('title', 'Vacancy Form Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Vacancy Form Details</h3>
                    <div>
                        <a href="{{ route('admin.vacancy-forms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        @if($form->is_read)
                            <form method="POST" action="{{ route('admin.vacancy-forms.mark-unread', $form->id) }}" 
                                  style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning" 
                                        onclick="return confirm('Mark as unread?')">
                                    <i class="fas fa-envelope"></i> Mark as Unread
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.vacancy-forms.mark-read', $form->id) }}" 
                                  style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success" 
                                        onclick="return confirm('Mark as read?')">
                                    <i class="fas fa-check"></i> Mark as Read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $form->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>
                                        <a href="mailto:{{ $form->email }}">{{ $form->email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile:</strong></td>
                                    <td>
                                        <a href="tel:{{ $form->mobile }}">{{ $form->mobile }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($form->is_read)
                                            <span class="badge bg-success">Read</span>
                                            @if($form->read_at)
                                                <small class="text-muted">({{ $form->read_at->format('d.m.Y H:i') }})</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">Unread</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Submitted:</strong></td>
                                    <td>{{ $form->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Vacancy Information</h5>
                            <table class="table table-borderless">
                                @if($form->vacancy)
                                    <tr>
                                        <td><strong>Position:</strong></td>
                                        <td>{{ $form->vacancy->translate()->position ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Experience:</strong></td>
                                        <td>{{ $form->vacancy->translate()->experience ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Work Schedule:</strong></td>
                                        <td>{{ $form->vacancy->translate()->work_schedule ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Apply Date:</strong></td>
                                        <td>{{ $form->vacancy->last_apply_date ? $form->vacancy->last_apply_date->format('d.m.Y') : 'N/A' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-muted">Vacancy has been deleted</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Message</h5>
                            <div style="color: black;" class="border p-3 rounded bg-light">
                                {{ $form->message }}
                            </div>
                        </div>
                    </div>

                    @if($form->file)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Attached File</h5>
                                <div class="border p-3 rounded bg-light">
                                    <p><strong>File:</strong> {{ $form->file_name }}</p>
                                    <a href="{{ route('admin.vacancy-forms.download-file', $form->id) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download File
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <form method="POST" action="{{ route('admin.vacancy-forms.destroy', $form->id) }}" 
                                      onsubmit="return confirm('Are you sure you want to delete this form? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> 
                                    </button>
                                </form>
                                
                                <div>
                                    <a href="mailto:{{ $form->email }}?subject=Re: Your Job Application" 
                                       class="btn btn-info">
                                        <i class="fas fa-reply"></i> 
                                    </a>
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
