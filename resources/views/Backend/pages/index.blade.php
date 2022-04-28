@extends('Backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="home-tab">

                    <div class="tab-content tab-content-basic">
                        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="statistics-details align-items-center justify-content-between">
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="statistics-title">Total Users <i
                                                        class="fa-solid fa-user icons"></i></p>
                                                <h3 class="rate-percentage">{{ $user_count }}</h3>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="statistics-details align-items-center justify-content-between">
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="statistics-title">Total Videos <i
                                                        class="fa-solid fa-video icons"></i></p>
                                                <h3 class="rate-percentage">{{ $video_count }}</h3>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-4">
                                    <div class="statistics-details align-items-center justify-content-between">
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="statistics-title">Total Hashtags <i class="fa-solid fa-hashtag icons"></i></p>
                                                <h3 class="rate-percentage">{{ $hashtag_count }}</h3> 
                                                {{-- <div class="row">
                                                    <div class="col-sm-6">
                                                        <h3 class="rate-percentage"> <span
                                                                style="font-size: 10px;">Like</span> {{ $like_count }}
                                                        </h3>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <h3 class="rate-percentage"> <span
                                                                style="font-size: 10px;">Comments</span>
                                                            {{ $comment_count }}</h3>
                                                    </div>
                                                   
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-sm-4">
                                    <div class="statistics-details align-items-center justify-content-between">
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="statistics-title">User Activities <i
                                                        class="fa-solid fa-align-justify icons"></i></p>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <h3 class="rate-percentage"> <span
                                                                style="font-size: 10px;">Like</span> {{ $like_count }}
                                                        </h3>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <h3 class="rate-percentage"> <span
                                                                style="font-size: 10px;">Comments</span>
                                                            {{ $comment_count }}</h3>
                                                    </div>
                                                   
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            <div class="row">
                                {{-- <div class="col-lg-8 d-flex flex-column">
                                    <div class="row flex-grow">
                                        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
                                            <div class="card card-rounded">
                                                <div class="card-body">
                                                    <div class="d-sm-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h4 class="card-title card-title-dash">
                                                                Users Registration</h4>
                                                            <h5 class="card-subtitle card-subtitle-dash">
                                                                Lorem Ipsum is simply dummy text of the
                                                                printing</h5>
                                                        </div>
                                                        <div id="performance-line-legend"></div>
                                                    </div>
                                                    <div class="chartjs-wrapper mt-5">
                                                        <canvas id="performaneLine"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}



                                {{-- <div class="row"> --}}
                                <div class="col-lg-8 d-flex flex-column">


                                    <div class="row flex-grow">
                                        <div class="col-12 grid-margin stretch-card">
                                            <div class="card card-rounded">
                                                <div class="card-body">
                                                    <div class="d-sm-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h4 class="card-title card-title-dash">Recent Users</h4>
                                                            {{-- <p class="card-subtitle card-subtitle-dash">You
                                                                    have 50+ new requests</p> --}}
                                                        </div>
                                                        {{-- <div>
                                                                <button class="btn btn-primary btn-lg text-white mb-0 me-0"
                                                                    type="button"><i class="mdi mdi-account-plus"></i>Add
                                                                    new member</button>
                                                            </div> --}}
                                                    </div>
                                                    <div class="table-responsive  mt-1">
                                                        <table class="table select-table">
                                                            <thead>
                                                                <tr>

                                                                    <th>Users</th>
                                                                    <th>Joined Date</th>
                                                                    <th>Phone no</th>
                                                                    <th>Email</th>
                                                                    <th>Location</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($recent_users as $item)
                                                                    <tr>

                                                                        <td>
                                                                            <div class="d-flex ">
                                                                                <img src="{{ asset('uploads/avtars/' . $item->profile_image) }}"
                                                                                    alt="{{ $item->username }}">
                                                                                <div>
                                                                                    <h6>{{ $item->first_name . ' ' . $item->last_name }}
                                                                                    </h6>
                                                                                    <p>{{ $item->username }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <h6>{{ date('j \\ F Y', strtotime($item->created_at)) }}
                                                                            </h6>
                                                                            <p>Joined</p>
                                                                        </td>
                                                                        <td>
                                                                            <h6>{{ $item->phone_no != '' ? $item->phone_no : '-' }}
                                                                            </h6>
                                                                            <p>Phone no</p>
                                                                        </td>
                                                                        <td>
                                                                            <h6>{{ $item->email != '' ? $item->email : '-' }}
                                                                            </h6>
                                                                            <p>Email</p>
                                                                        </td>
                                                                        <td>
                                                                            <h6>{{ $item->country != '' ? $item->country : '-' }}
                                                                            </h6>
                                                                            <p>Location</p>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5">No record found.</td>
                                                                    </tr>
                                                                @endforelse


                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- </div> --}}
                                <div class="col-lg-4 d-flex flex-column">
                                    <div class="row flex-grow">
                                        <div class="col-12 grid-margin stretch-card">
                                            <div class="card card-rounded">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <h4 class="card-title card-title-dash">New Tickets</h4>
                                                                <div class="add-items d-flex mb-0">
                                                                    <!-- <input type="text" class="form-control todo-list-input" placeholder="What do you need to do today?"> -->

                                                                </div>
                                                            </div>
                                                            <div class="list-wrapper">
                                                                <ul class="todo-list todo-list-rounded">
                                                                    <li class="d-block">
                                                                        <div class="form-check w-100">
                                                                            <label class="form-check-label">
                                                                                <input class="checkbox"
                                                                                    type="checkbox"> Lorem
                                                                                Ipsum is simply dummy text
                                                                                of the printing <i
                                                                                    class="input-helper rounded"></i>
                                                                            </label>
                                                                            <div class="d-flex mt-2">
                                                                                <div class="ps-4 text-small me-3">
                                                                                    24 June 2020</div>
                                                                                <div
                                                                                    class="badge badge-opacity-warning me-3">
                                                                                    Due tomorrow</div>
                                                                                <i class="mdi mdi-flag ms-2 flag-color"></i>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li class="d-block">
                                                                        <div class="form-check w-100">
                                                                            <label class="form-check-label">
                                                                                <input class="checkbox"
                                                                                    type="checkbox"> Lorem
                                                                                Ipsum is simply dummy text
                                                                                of the printing <i
                                                                                    class="input-helper rounded"></i>
                                                                            </label>
                                                                            <div class="d-flex mt-2">
                                                                                <div class="ps-4 text-small me-3">
                                                                                    23 June 2020</div>
                                                                                <div
                                                                                    class="badge badge-opacity-success me-3">
                                                                                    Done</div>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <div class="form-check w-100">
                                                                            <label class="form-check-label">
                                                                                <input class="checkbox"
                                                                                    type="checkbox"> Lorem
                                                                                Ipsum is simply dummy text
                                                                                of the printing <i
                                                                                    class="input-helper rounded"></i>
                                                                            </label>
                                                                            <div class="d-flex mt-2">
                                                                                <div class="ps-4 text-small me-3">
                                                                                    24 June 2020</div>
                                                                                <div
                                                                                    class="badge badge-opacity-success me-3">
                                                                                    Done</div>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li class="border-bottom-0">
                                                                        <div class="form-check w-100">
                                                                            <label class="form-check-label">
                                                                                <input class="checkbox"
                                                                                    type="checkbox"> Lorem
                                                                                Ipsum is simply dummy text
                                                                                of the printing <i
                                                                                    class="input-helper rounded"></i>
                                                                            </label>
                                                                            <div class="d-flex mt-2">
                                                                                <div class="ps-4 text-small me-3">
                                                                                    24 June 2020</div>
                                                                                <div
                                                                                    class="badge badge-opacity-danger me-3">
                                                                                    Expired</div>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('extrajs')
    @endsection
