@extends('admin.layout.app')

@section('title', __('Vacancy Forms'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Vacancy Forms') }}</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('admin.vacancy-forms.index') }}">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" 
                                           value="{{ request('search') }}" placeholder="{{ __('Search by name, email, mobile...') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('admin.vacancy-forms.index') }}">
                                <select name="vacancy_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">{{ __('All Vacancies') }}</option>
                                    @foreach($vacancies as $vacancy)
                                        <option value="{{ $vacancy->id }}" 
                                                {{ request('vacancy_id') == $vacancy->id ? 'selected' : '' }}>
                                            {{ $vacancy->translate()->position ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('admin.vacancy-forms.index') }}">
                                <select name="is_read" class="form-select" onchange="this.form.submit()">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>{{ __('Unread') }}</option>
                                    <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>{{ __('Read') }}</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <a style="width: max-content" href="{{ route('admin.vacancy-forms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> 
                            </a>
                        </div>
                    </div>

                    <!-- Forms Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Vacancy') }}</th>
                                    <th>{{ __('File') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($forms as $form)
                                    <tr class="{{ !$form->is_read ? 'table-warning' : '' }}">
                                        <td>{{ $form->id }}</td>
                                        <td>{{ $form->name }}</td>
                                        <td>{{ $form->email }}</td>
                                        <td>{{ $form->mobile }}</td>
                                        <td>
                                            @if($form->vacancy)
                                                {{ $form->vacancy->translate()->position ?? 'N/A' }}
                                            @else
                                                <span class="text-muted">{{ __('Deleted Vacancy') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($form->file)
                                                <a target="_blank" href="{{ asset('storage/' . $form->file) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> {{ __('Download') }}
                                                </a>
                                            @else
                                                <span class="text-muted">{{ __('No file') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($form->is_read)
                                                <span class="badge bg-success">{{ __('Read') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('Unread') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $form->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.vacancy-forms.show', $form->id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($form->is_read)
                                                    <form method="POST" action="{{ route('admin.vacancy-forms.mark-unread', $form->id) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-warning" 
                                                                onclick="return confirm('{{ __('Mark as unread?') }}')">
                                                            <i class="fas fa-envelope"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.vacancy-forms.mark-read', $form->id) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" 
                                                                onclick="return confirm('{{ __('Mark as read?') }}')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.vacancy-forms.destroy', $form->id) }}" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this form?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">{{ __('No forms found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $forms->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
