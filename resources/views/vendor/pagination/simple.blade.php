@if ($paginator->hasPages())
    <ul class="pagination" role="navigation">
        @foreach ($paginator->links() as $link)
            <li class="page-item {{ $link['active'] ? 'active' : '' }}">
                <a class="page-link" href="{{ $link['url'] ?? '#' }}">{{ $link['label'] }}</a>
            </li>
        @endforeach
    </ul>
@endif
