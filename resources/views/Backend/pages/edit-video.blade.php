@extends('Backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="row">

            <div class="col-md-4 grid-margin sstretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Status</h4>
                        <p class="card-description">
                            Edit Status
                        </p>
                        <form class="forms-sample" action="{{ route('video.status') }}" method="POST">
                            @csrf
                            <input type="hidden" name="video_id" value="{{ $video->id }}">
                            <div class="form-group">
                                <label>Video Approval Status</label>
                                <select class="form-control" name="is_approved">
                                    <option value="1" {{ $video->is_approved== 1 ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="0" {{ $video->is_approved == 0 ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>

                            {{-- <div class="form-group">
                                <label>Ban user </label>
                                <select class="form-control" name="ban_status">
                                    <option value="yes" {{ $user->ban_time != null ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ $user->ban_time == null ? 'selected' : '' }}>No</option>


                                </select>
                            </div> --}}


                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                            <button class="btn btn-light">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
