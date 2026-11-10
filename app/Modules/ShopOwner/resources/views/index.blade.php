@extends('layouts.app-shop')

@section('title', 'Shop owners')

@section('content')
    <div class="page-header">
        <div>
            <h1>Shop owners</h1>
            <p>Accounts that can log in to manage their own shop.</p>
        </div>
        <a href="{{ route('admin.shop-owners.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add shop owner
        </a>
    </div>

    <div class="surface overflow-hidden">
        @if ($owners->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-people"></i></div>
                <h2 class="h5">No shop owners yet</h2>
                <p class="text-secondary">Create an account for each person who runs a shop.</p>
                <a href="{{ route('admin.shop-owners.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add shop owner
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-admin align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Shop</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($owners as $owner)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($owner->name, 0, 1)) }}</span>
                                        <span class="cell-title">{{ $owner->name }}</span>
                                    </div>
                                </td>
                                <td class="text-secondary">{{ $owner->email }}</td>
                                <td>
                                    @if ($owner->shop)
                                        <span class="tag"><i class="bi bi-shop"></i>{{ $owner->shop->name }}</span>
                                    @else
                                        <span class="text-secondary small">No shop</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($owner->is_active)
                                        <span class="badge rounded-pill bg-success-subtle text-success-emphasis">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">Disabled</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @include('admin.partials.row-actions', [
                                        'editUrl' => route('admin.shop-owners.edit', $owner),
                                        'deleteUrl' => route('admin.shop-owners.destroy', $owner),
                                        'confirm' => 'Delete this shop owner account? This cannot be undone.',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('admin.partials.pagination-footer', ['paginator' => $owners, 'label' => 'shop owners'])
        @endif
    </div>
@endsection
