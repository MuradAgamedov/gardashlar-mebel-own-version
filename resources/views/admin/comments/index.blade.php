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
                    @if($comment->status === 'read')
                        <span class="badge bg-success">Oxundu</span>
                    @else
                        <form action="{{ route('admin.comments.read', $comment->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning" title="Oxundu kimi qeyd et">
                                <i class="bi bi-check2-circle"></i>
                            </button>
                        </form>
                    @endif
                </td>
                <td>
                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Əminsiniz?')">
                            <i class="bi bi-trash"></i>
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
