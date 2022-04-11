@extends('Backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-4 grid-margin sstretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Upload Banners</h4>
                        <p class="card-description">
                            Save Banners
                        </p>
                        <form class="forms-sample" action="{{ route('banners.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>File upload</label>
                                <input type="file" name="banners[]" class="file-upload-default" multiple>
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled=""
                                        placeholder="Upload Image">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary btn-sm"
                                            type="button">Upload</button>
                                    </span>
                                </div>
                                <span class="text-danger" style="font-size: small;font-weight: 700;">
                                    @error('banners')
                                        This field is required.
                                    @enderror
                                </span>

                            </div>
                            <button type="submit" class="btn btn-primary me-2">Submit</button>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8 grid-margin sstretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Banners</h4>
                        <p class="card-description">
                            Save Banners
                        </p>
                        @foreach (json_decode($banners->image_name) as $item)
                            <div style="margin-bottom: 5px;">
                                <img src="{{ asset('uploads/banners/' . $item) }}" style="width: 100%;border-radius:30px;"
                                    alt="">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
