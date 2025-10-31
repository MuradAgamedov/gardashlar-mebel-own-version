@extends('admin.layout.app')

@section('content')

{{-- Bootstrap 5 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.trash-btn {
    background: none;
    border: none;
    cursor: pointer;
    transition: transform 0.2s ease, color 0.2s ease;
}
.trash-icon {
    width: 18px;
    height: 18px;
    display: inline-block;
    position: relative;
}
.trash-icon::before, .trash-icon::after {
    content: '';
    position: absolute;
    background: #555;
    transition: background 0.2s;
}
.trash-icon::before {
    width: 12px;
    height: 10px;
    bottom: 0;
    left: 3px;
    border: 1.5px solid #555;
    border-top: none;
    border-radius: 1px 1px 2px 2px;
}
.trash-icon::after {
    width: 12px;
    height: 2px;
    top: 0;
    left: 3px;
    border-radius: 2px;
    background: #555;
}
.trash-icon span {
    position: absolute;
    top: 2px;
    left: 8px;
    width: 2px;
    height: 6px;
    background: #555;
    border-radius: 1px;
    transition: background 0.2s;
}

/* Hover efekti */
.trash-btn:hover .trash-icon::before,
.trash-btn:hover .trash-icon::after,
.trash-btn:hover .trash-icon span {
    background: #dc3545;
    border-color: #dc3545;
}
.trash-btn:hover {
    transform: scale(1.1);
}
</style>

<div class="container mt-4">
    <h4 class="mb-3">İstifadəçilər Siyahısı</h4>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Bağla"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle mb-0">
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
                                    <button type="submit" class="trash-btn" title="Sil">
                                        <div class="trash-icon">
                                            <span></span>
                                        </div>
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
    </div>
</div>

{{-- Bootstrap 5 JS (Alert və s. üçün) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection
