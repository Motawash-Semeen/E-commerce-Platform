@extends('layouts.app-shop')

@section('title', 'Edit product')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <a href="{{ route('admin.products.index') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to products
            </a>

            <div class="page-header">
                <div>
                    <h1>Edit product</h1>
                    <p>Editing <strong>{{ $product->name }}</strong>.</p>
                </div>
            </div>

            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="surface p-4 p-lg-5">
                @csrf
                @method('PUT')

                @include('Product::_form')

                <div class="d-flex flex-wrap gap-2 justify-content-end border-top pt-4 mt-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
