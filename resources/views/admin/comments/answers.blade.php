@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <h4>{{ __('Comment Replies') }}</h4>
    <p><strong>{{ __('Comment:') }}</strong> {{ $comment->text }}</p>

    <!-- Cavab əlavə et form -->
    <form action="{{ route('admin.comments.answers.store', $comment->id) }}" method="POST">
        @csrf
        <textarea name="text" class="form-control" placeholder="{{ __('Write a reply') }}" required></textarea>
        <button type="submit" class="btn btn-primary mt-2">{{ __('Add Reply') }}</button>
    </form>

    <hr>

    <h5>{{ __('Existing Replies') }}</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Reply Text') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($answers as $answer)
            <tr>
                <td>{{ $answer->id }}</td>
                <td>{{ $answer->text }}</td>
                <td>
                    <form action="{{ route('admin.comments.answers.destroy', $answer->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
