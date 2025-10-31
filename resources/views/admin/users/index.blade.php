@extends('admin.layout.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">İstifadəçilər Siyahısı</h4>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Yaradılma tarixi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name ?: '-' }}</td>
                    <td>{{ $user->surname ?: '-' }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?: '-' }}</td>
                    <td>{{ $user->created_at ? $user->created_at->format('d.m.Y H:i') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">İstifadəçi tapılmadı</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
