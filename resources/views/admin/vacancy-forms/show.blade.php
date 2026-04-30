@extends('admin.layout.app')

@section('title', __('Vacancy Form Details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('Vacancy Form Details') }}</h3>
                    <div>
                        <a href="{{ route('admin.vacancy-forms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                        </a>
                        @if($form->is_read)
                            <form method="POST" action="{{ route('admin.vacancy-forms.mark-unread', $form->id) }}" 
                                  style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning" 
                                        onclick="return confirm('{{ __('Mark as unread?') }}')">
                                    <i class="fas fa-envelope"></i> {{ __('Mark as Unread') }}
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.vacancy-forms.mark-read', $form->id) }}" 
                                  style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success" 
                                        onclick="return confirm('{{ __('Mark as read?') }}')">
                                    <i class="fas fa-check"></i> {{ __('Mark as Read') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('Personal Information') }}</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>{{ __('Name:') }}</strong></td>
                                    <td>{{ $form->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Email:') }}</strong></td>
                                    <td>
                                        <a href="mailto:{{ $form->email }}">{{ $form->email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Mobile:') }}</strong></td>
                                    <td>
                                        <a href="tel:{{ $form->mobile }}">{{ $form->mobile }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Status:') }}</strong></td>
                                    <td>
                                        @if($form->is_read)
                                            <span class="badge bg-success">{{ __('Read') }}</span>
                                            @if($form->read_at)
                                                <small class="text-muted">({{ $form->read_at->format('d.m.Y H:i') }})</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">{{ __('Unread') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('Submitted:') }}</strong></td>
                                    <td>{{ $form->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('Vacancy Information') }}</h5>
                            <table class="table table-borderless">
                                @if($form->vacancy)
                                    <tr>
                                        <td><strong>{{ __('Position:') }}</strong></td>
                                        <td>{{ $form->vacancy->translate()->position ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Experience:') }}</strong></td>
                                        <td>{{ $form->vacancy->translate()->experience ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Work Schedule:') }}</strong></td>
                                        <td>{{ $form->vacancy->translate()->work_schedule ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Last Apply Date:') }}</strong></td>
                                        <td>{{ $form->vacancy->last_apply_date ? $form->vacancy->last_apply_date->format('d.m.Y') : 'N/A' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-muted">{{ __('Vacancy has been deleted') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('Message') }}</h5>
                            <div style="color: black;" class="border p-3 rounded">
                                {{ $form->message }}
                            </div>
                        </div>
                    </div>

                    @if($form->file)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>{{ __('Attached File') }}</h5>
                                <div class="border p-3 rounded bg-light">
                                    <p><strong>{{ __('File:') }}</strong> {{ $form->file_name }}</p>
                                    <a href="{{ asset('storage/' . $form->file) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-download"></i> {{ __('Download File') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <form method="POST" action="{{ route('admin.vacancy-forms.destroy', $form->id) }}" 
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this form? This action cannot be undone.') }}')">
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
