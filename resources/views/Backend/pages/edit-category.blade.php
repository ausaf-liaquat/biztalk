@extends('Backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="row">

            <div class="col-md-4 grid-margin sstretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"> Edit Category</h4>
                        <p class="card-description">
                            Edit Price range
                        </p>
                        <form class="forms-sample" action="{{ route('category.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id }}">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" class="form-control" id="category" placeholder="Price range e.g (0000 - 9999)"
                                    name="category" value="{{ $category->price_range }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                            <a class="btn btn-light" href="{{ route('category.index') }}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
