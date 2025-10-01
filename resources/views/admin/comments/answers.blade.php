@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <h4>Şərh üçün cavablar</h4>
    <p><strong>Şərh:</strong> {{ $comment->text }}</p>

    <!-- Cavab əlavə et form -->
    <form action="{{ route('comments.answers.store', $comment->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <textarea name="text" class="form-control" placeholder="Cavab yazın" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Cavab əlavə et</button>
    </form>

    <hr>

    <h5>Mövcud cavablar</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>İstifadəçi</th>
                <th>Cavab mətni</th>
                <th>Əməliyyatlar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($answers as $answer)
            <tr>
                <td>{{ $answer->id }}</td>
                <td>{{ $answer->user->name ?? '---' }}</td>
                <td>{{ $answer->text }}</td>
                <td>
                    <form action="{{ route('comments.answers.destroy', $answer->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Əminsiniz?')">
                            Sil
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
