@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <h4>{{ __('Comments') }}</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('User') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Product') }}</th>
                <th>{{ __('Product ID') }}</th>
                <th>{{ __('Comment Text') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comments as $comment)
            <tr>
                <td>{{ $comment->id }}</td>
                <td>{{ $comment->user->name ?? '---' }}</td>
                <td>{{ $comment->user->email ?? '---' }}</td>
                <td>{{ $comment->product->title ?? '---' }}</td>
                <td>{{ $comment->product->id ?? '---' }}</td>
                <td>{{ $comment->text }}</td>
                <td>
                    <!-- Approve button -->
                    @if($comment->status === 'approved')
                        <a href="{{ route('admin.comments.approve', $comment->id) }}" 
                           class="btn btn-sm btn-primary" 
                           title="{{ __('Cancel Approval') }}">
                           {{ __('Approved') }}
                        </a>
                    @else
                        <a href="{{ route('admin.comments.approve', $comment->id) }}" 
                           class="btn btn-sm btn-secondary" 
                           title="{{ __('Approve') }}">
                           {{ __('Not Approved') }}
                        </a>
                    @endif

                    <!-- Read/Unread button -->
                    @if($comment->is_read === 'active')
                        <a href="{{ route('admin.comments.read', $comment->id) }}" class="btn btn-sm btn-success">
                            {{ __('Read') }}
                        </a>
                    @else
                        <a href="{{ route('admin.comments.read', $comment->id) }}" class="btn btn-sm btn-warning">
                            {{ __('Unread') }}
                        </a>
                    @endif
                </td>
                <td>
                    <!-- Delete button -->
                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                              <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5v-7zM4.118 4 4 4.059V4h8v.059L11.882 4H4.118zM2.5 3h11a.5.5 0 0 1 0 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a.5.5 0 0 1 0-1z"/>
                            </svg>
                        </button>
                    </form>

                  <!-- Reply button -->
<a href="{{ route('admin.comments.answers', $comment->id) }}" class="btn btn-sm btn-info" title="{{ __('Reply') }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply" viewBox="0 0 16 16">
        <path d="M6.598 5.013a.5.5 0 0 1 .708.706L4.707 8H12.5a.5.5 0 0 1 0 1H4.707l2.599 2.281a.5.5 0 1 1-.707.706l-3.5-3.075a.5.5 0 0 1 0-.707l3.5-3.075z"/>
    </svg>
</a>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $comments->links() }}
</div>
@endsection
