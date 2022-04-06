@extends('Backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit</h4>
                        <p class="card-description">
                            You can edit user details
                        </p>
                        <form class="forms-sample" autocomplete="off">
                            <div class="form-group">
                                <label for="exampleInputUsername1">Username</label>
                                <input type="text" class="form-control" id="exampleInputUsername1" placeholder="Username"
                                    name="username">
                            </div>
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name" placeholder="First name">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" placeholder="Last name"
                                    name="last_name">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
                            </div>

                            <div class="form-group">
                                <label>File upload</label>
                                <input type="file" name="img[]" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled=""
                                        placeholder="Upload Image">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary btn-sm" type="button">Upload</button>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                            <button class="btn btn-light">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Status</h4>
                        <p class="card-description">
                            Edit Status
                        </p>
                        <form class="forms-sample">
                            <div class="form-group row">
                                <label for="exampleInputUsername2" class="col-sm-4 col-form-label">Email</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="exampleInputUsername2"
                                        placeholder="Username">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputEmail2" class="col-sm-4 col-form-label">Email</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" id="exampleInputEmail2" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-4 col-form-label">Mobile</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="exampleInputMobile"
                                        placeholder="Mobile number">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputPassword2" class="col-sm-4 col-form-label">Password</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="exampleInputPassword2"
                                        placeholder="Password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="exampleInputConfirmPassword2" class="col-sm-4 col-form-label">Re
                                    Password</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="exampleInputConfirmPassword2"
                                        placeholder="Password">
                                </div>
                            </div>
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input">
                                    Remember me
                                    <i class="input-helper"></i></label>
                            </div>
                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                            <button class="btn btn-light">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
