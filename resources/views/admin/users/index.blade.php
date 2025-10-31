@extends('admin.layout.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@section('content')
<div class="container mt-4">
    <h4 class="mb-3">İstifadəçilər Siyahısı</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Yaradılma tarixi</th>
                <th class="text-center">Əməliyyatlar</th>
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
                    <td>{{ $user->created_at?->format('d.m.Y H:i') ?: '-' }}</td>
                    <td class="text-center">
                        <form action="{{ route('admin.registered-users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bu istifadəçini silmək istədiyinizə əminsiniz?');" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">İstifadəçi tapılmadı</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
