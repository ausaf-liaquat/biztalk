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
                        <form class="forms-sample" action="{{ route('user.update') }}" method="post" autocomplete="off">
                            @csrf
                            <input type="hidden" value="{{ $user->id }}" name="id">
                            <div class="form-group">
                                <img src="{{ asset('uploads/avtars/' . $user->profile_image) }}" alt=""
                                    style="border-radius: 12px;width: 100px;">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Username</label>
                                <input type="text" class="form-control" id="exampleInputUsername1" onkeyup="duplicateUsername(this)" placeholder="Username"
                                    name="username" value="{{ $user->username }}">
                                    <span class="text-danger mb-4 username-error" id="username-error"
                                    style="font-size: small;"></span>
                            </div>
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name" placeholder="First name"
                                    name="first_name" value="{{ $user->first_name }}">
                                <span class="text-danger" style="font-size: small;font-weight: 700;">
                                    @error('first_name')
                                        This field is required.
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" placeholder="Last name"
                                    name="last_name" value="{{ $user->last_name }}">
                                <span class="text-danger" style="font-size: small;font-weight: 700;">
                                    @error('last_name')
                                        This field is required.
                                    @enderror
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" class="form-control" id="exampleInputEmail1"
                                    onkeyup="duplicateEmail(this)" placeholder="Email" value="{{ $user->email }}"
                                    name="email">
                                <span class="text-danger mb-4 email-error" id="email-error"
                                    style="font-size: small;"></span>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone no</label>
                                <input type="text" class="form-control" id="phone" onkeyup="duplicatePhone(this)" placeholder="Phone"
                                    value="{{ $user->phone_no }}" name="phone">
                                    <span class="text-danger mb-4 phone-error" id="phone-error"
                                    style="font-size: small;"></span>
                            </div>

                            {{-- <div class="form-group">
                                <label>File upload</label>
                                <input type="file" name="img[]" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled=""
                                        placeholder="Upload Image">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary btn-sm" type="button">Upload</button>
                                    </span>
                                </div>
                                
                            </div> --}}


                            <button type="submit" class="btn btn-primary me-2" id="edit">Submit</button>
                            <a class="btn btn-light" href="{{ route('user.index') }}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin sstretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Status</h4>
                        <p class="card-description">
                            Edit Status
                        </p>
                        <form class="forms-sample" action="{{ route('user.status') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="form-group">
                                <label>Account Status</label>
                                <select class="form-control" name="status">
                                  <option value="active" {{ ($user->is_verified=='active')?'selected':'' }}>Active</option>
                                  <option value="pending" {{ ($user->is_verified=='pending')?'selected':'' }}>Pending</option>
                                  
                                  
                                </select>
                              </div>

                              <div class="form-group">
                                <label>Ban user </label>
                                <select class="form-control" name="ban_status">
                                  <option value="yes" {{ ($user->ban_time!=null)?'selected':'' }}>Yes</option>
                                  <option value="no" {{ ($user->ban_time==null)?'selected':'' }}>No</option>
                                  
                                  
                                </select>
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
@section('extrajs')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script>
        // Duplicate Email Checker
        function duplicateEmail(element) {
            var email = $(element).val();
            $.ajax({
                type: "POST",
                url: "{{ route('user.checkemail') }}",
                data: {
                    email: email
                },
                dataType: "json",
                success: function(res) {
                    if (res.exists) {

                        $('#email-error')
                            .css('color', 'red')
                            .html("This Email already exists!");
                        $('#email-error')
                            .prop('hidden', false);
                        $('#edit').prop('disabled', true);

                    } else {
                        $('#email-error')
                            .prop('hidden', true);
                        $('#edit').prop('disabled', false);

                    }
                },
                error: function(jqXHR, exception) {

                }
            });
        }
        function duplicatePhone(element) {
            var phone = $(element).val();
            $.ajax({
                type: "POST",
                url: "{{ route('user.checkemail') }}",
                data: {
                    phone: phone
                },
                dataType: "json",
                success: function(res) {
                    if (res.exists) {

                        $('#phone-error')
                            .css('color', 'red')
                            .html("This phone no already exists!");
                        $('#phone-error')
                            .prop('hidden', false);
                        $('#edit').prop('disabled', true);

                    } else {
                        $('#phone-error')
                            .prop('hidden', true);
                        $('#edit').prop('disabled', false);

                    }
                },
                error: function(jqXHR, exception) {

                }
            });
        }
        function duplicateUsername(element) {
            var username = $(element).val();
            $.ajax({
                type: "POST",
                url: "{{ route('user.checkemail') }}",
                data: {
                    username: username
                },
                dataType: "json",
                success: function(res) {
                    if (res.exists) {

                        $('#username-error')
                            .css('color', 'red')
                            .html("This username no already exists!");
                        $('#username-error')
                            .prop('hidden', false);
                        $('#edit').prop('disabled', true);

                    } else {
                        $('#username-error')
                            .prop('hidden', true);
                        $('#edit').prop('disabled', false);

                    }
                },
                error: function(jqXHR, exception) {

                }
            });
        }
    </script>
@endsection
