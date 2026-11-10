@extends('layouts.app-shop')

@section('title', 'Add shop owner')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <a href="{{ route('admin.shop-owners.index') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to shop owners
            </a>

            <div class="page-header">
                <div>
                    <h1>Add shop owner</h1>
                    <p>Create a login for someone who runs a shop.</p>
                </div>
            </div>

            <form action="{{ route('admin.shop-owners.store') }}" method="POST" class="surface p-4 p-lg-5">
                @csrf

                @include('ShopOwner::_form')

                <div class="d-flex flex-wrap gap-2 justify-content-end border-top pt-4 mt-2">
                    <a href="{{ route('admin.shop-owners.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save shop owner</button>
                </div>
            </form>
        </div>
    </div>
@endsection
