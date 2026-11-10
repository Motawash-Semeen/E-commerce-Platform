{{-- Edit + Delete buttons for a table row.
     Expects: $editUrl, $deleteUrl, $confirm (plain text, no user data). --}}
<div class="d-inline-flex gap-1">
    <a href="{{ $editUrl }}" class="btn btn-light-primary btn-icon" title="Edit">
        <i class="bi bi-pencil-square"></i><span class="visually-hidden">Edit</span>
    </a>

    <form action="{{ $deleteUrl }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $confirm }}');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-icon" title="Delete">
            <i class="bi bi-trash3"></i><span class="visually-hidden">Delete</span>
        </button>
    </form>
</div>
