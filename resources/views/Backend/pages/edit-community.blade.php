@extends('Backend.layouts.app')
@section('css')
    <style>
        textarea.form-control{
            min-height: 17rem;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="row">

            <div class="col-md-6 grid-margin sstretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"> Edit Community Guidelines</h4>
                        <p class="card-description">
                            Edit Terms of Services
                        </p>
                        <form class="forms-sample" action="{{ route('community.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="cg_id" value="{{ $cg->id }}">
                          
                            <div class="form-group">
                                <label for="tos">Terms of Service</label>
                              
                                    <textarea class="form-control" name="tos" id="tos" cols="50" rows="150">{{ $cg->tos }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary me-2" id="submit">Submit</button>
                            <a class="btn btn-light" href="{{ route('community.index') }}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection