@extends('admin.layout.app')

@section('content')
<div class="page-body">
    <h4>Şərh detalları</h4>
    <p><b>ID:</b> {{ $comment->id }}</p>
    <p><b>İstifadəçi:</b> {{ $comment->user->name ?? '---' }}</p>
    <p><b>Məhsul:</b> {{ $comment->product->title ?? '---' }}</p>
    <p><b>Mətn:</b> {{ $comment->text }}</p>
    <p><b>Status:</b> {{ $comment->status }}</p>
</div>
@endsection
