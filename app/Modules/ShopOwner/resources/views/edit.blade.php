@extends('layouts.app-shop')

@section('title', 'Edit shop owner')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <a href="{{ route('admin.shop-owners.index') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to shop owners
            </a>

            <div class="page-header">
                <div>
                    <h1>Edit shop owner</h1>
                    <p>Editing <strong>{{ $owner->name }}</strong>.</p>
                </div>
            </div>

            <form action="{{ route('admin.shop-owners.update', $owner) }}" method="POST" class="surface p-4 p-lg-5">
                @csrf
                @method('PUT')

                @include('ShopOwner::_form')

                <div class="d-flex flex-wrap gap-2 justify-content-end border-top pt-4 mt-2">
                    <a href="{{ route('admin.shop-owners.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
