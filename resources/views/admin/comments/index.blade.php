@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <h4>Şərhlər</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>İstifadəçi</th>
                <th>Email</th>
                <th>Məhsul</th>
                <th>Məhsul ID</th>
                <th>Şərh mətni</th>
                <th>Status</th>
                <th>Əməliyyatlar</th>
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
                    @dd($comment->is_read)
                @if((int) $comment->is_read === 1)
    <span class="badge bg-success">Oxundu</span>
@else
    <a href="{{ route('admin.comments.read', $comment->id) }}" class="btn btn-sm btn-warning" title="Oxundu kimi qeyd et">
        Oxundu
    </a>
@endif


                </td>
                <td>
                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Əminsiniz?')">
                            <!-- Trash SVG icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                              <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5v-7zM4.118 4 4 4.059V4h8v.059L11.882 4H4.118zM2.5 3h11a.5.5 0 0 1 0 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a.5.5 0 0 1 0-1z"/>
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $comments->links() }}
</div>
@endsection
