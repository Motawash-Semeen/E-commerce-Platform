@extends('layouts.app-shop')

@section('title', 'Add category')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <a href="{{ route('admin.categories.index') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to categories
            </a>

            <div class="page-header">
                <div>
                    <h1>Add category</h1>
                    <p>Give the category a clear, unique name. Its URL slug is created for you.</p>
                </div>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="surface p-4 p-lg-5">
                @csrf

                @include('Category::_form')

                <div class="d-flex flex-wrap gap-2 justify-content-end border-top pt-4 mt-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save category</button>
                </div>
            </form>
        </div>
    </div>
@endsection
