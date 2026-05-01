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
                           <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-check-circle-fill me-1" viewBox="0 0 16 16">
                             <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                           </svg>
                           {{ __('Cancel Approval') }}
                        </a>
                    @else
                        <a href="{{ route('admin.comments.approve', $comment->id) }}" 
                           class="btn btn-sm btn-secondary" 
                           title="{{ __('Approve') }}">
                           <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-circle me-1" viewBox="0 0 16 16">
                             <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                           </svg>
                           {{ __('Approve') }}
                        </a>
                    @endif

                    <!-- Read/Unread button -->
                    @if($comment->is_read === 'active')
                        <a href="{{ route('admin.comments.read', $comment->id) }}" class="btn btn-sm btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-envelope-open-fill me-1" viewBox="0 0 16 16">
                              <path d="M8.941.435a2 2 0 0 0-1.882 0l-6 3.2A2 2 0 0 0 0 5.4v.314l6.709 3.976c.715.422 1.585.422 2.3 0L15.708 5.714V5.4a2 2 0 0 0-1.059-1.765l-6-3.2zM15.708 6.75l-7.3 4.333a2 2 0 0 1-2.143 0L0 6.75V13.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6.75z"/>
                            </svg>
                            {{ __('Mark as Unread') }}
                        </a>
                    @else
                        <a href="{{ route('admin.comments.read', $comment->id) }}" class="btn btn-sm btn-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-envelope-fill me-1" viewBox="0 0 16 16">
                              <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
                            </svg>
                            {{ __('Mark as Read') }}
                        </a>
                    @endif
                </td>
                <td class="d-flex gap-1">
                    <!-- Delete button -->
                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" style="width: 32px;" onclick="return confirm('{{ __('Are you sure?') }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                              <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5v-7zM4.118 4 4 4.059V4h8v.059L11.882 4H4.118zM2.5 3h11a.5.5 0 0 1 0 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a.5.5 0 0 1 0-1z"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Reply button -->
                    <a href="{{ route('admin.comments.answers', $comment->id) }}" class="btn btn-sm btn-info" style="width: 32px;" title="{{ __('Reply') }}">
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
