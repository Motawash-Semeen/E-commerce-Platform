{{-- "Showing 1–10 of 14" + page links under an admin table. Expects $paginator and $label. --}}
@if ($paginator->total() > 0)
    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 px-3 py-3 border-top">
        <span class="small text-secondary">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} {{ $label }}
        </span>
        {{ $paginator->onEachSide(1)->links('pagination::simple-bootstrap-5') }}
    </div>
@endif
