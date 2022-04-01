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
                                                <p class="statistics-title">Total Users <i class="mdi mdi-account icons"></i></p>
                                                <h3 class="rate-percentage">0</h3>
                                            </div>
                                        </div>
                                      

                                    </div>
                                </div>
                                <div class="col-sm-4">
                                        <div class="statistics-details align-items-center justify-content-between">
                                        <div class="card">
                                                  <div class="card-body">
                                                      <p class="statistics-title">Total Videos <i class="mdi mdi-message-video icons" ></i></p>
                                                      <h3 class="rate-percentage">0</h3>
                                                  </div>
                                              </div>
                                        </div>
                                              
                                </div>
                                <div class="col-sm-4">
                                        <div class="statistics-details align-items-center justify-content-between">
                                        <div class="card">
                                                  <div class="card-body">
                                                      <p class="statistics-title">User Activities <i class="mdi mdi-message-video icons"></i></p>
                                                      <h3 class="rate-percentage">0</h3>
                                                      
                                                      
                                                  </div>
                                              </div>
                                        </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-8 d-flex flex-column">
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
                                </div>

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

                                <div class="row">
                                    <div class="col-lg-12 d-flex flex-column">


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
                                                                        <th>
                                                                            Sr. no
                                                                        </th>
                                                                        <th>Users</th>
                                                                        <th>Joined Date</th>
                                                                        <th>Phone no</th>
                                                                        <th>Location</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            1
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex ">
                                                                                <img src="{{asset('assets/images/faces/face1.jpg')}}" alt="">
                                                                                <div>
                                                                                    <h6>Brandon Washington</h6>
                                                                                    <p>Head admin</p>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <h6>13 aug 2022</h6>
                                                                            <p>Joined</p>
                                                                        </td>
                                                                        <td>
                                                                            <h6>213-456-2345</h6>
                                                                            <p>Phone no</p>
                                                                        </td>
                                                                        <td>
                                                                            <h6>New Yourk</h6>
                                                                            <p>Location</p>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                           2
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex">
                                                                                <img src="{{asset('assets/images/faces/face2.jpg')}}" alt="">
                                                                                <div>
                                                                                    <h6>John Brooks</h6>
                                                                                    <p>Head admin</p>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                                <h6>13 aug 2022</h6>
                                                                                <p>Joined</p>
                                                                            </td>
                                                                            <td>
                                                                                <h6>213-456-2345</h6>
                                                                                <p>Phone no</p>
                                                                            </td>
                                                                            <td>
                                                                                <h6>New Yourk</h6>
                                                                                <p>Location</p>
                                                                            </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            3
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex">
                                                                                <img src="{{asset('assets/images/faces/face3.jpg')}}" alt="">
                                                                                <div>
                                                                                    <h6>Wayne Murphy</h6>
                                                                                    <p>Head admin</p>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                                <h6>13 aug 2022</h6>
                                                                                <p>Joined</p>
                                                                            </td>
                                                                            <td>
                                                                                <h6>213-456-2345</h6>
                                                                                <p>Phone no</p>
                                                                            </td>
                                                                            <td>
                                                                                <h6>New Yourk</h6>
                                                                                <p>Location</p>
                                                                            </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            4
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex">
                                                                                <img src="{{asset('assets/images/faces/face4.jpg')}}" alt="">
                                                                                <div>
                                                                                    <h6>Matthew Bailey</h6>
                                                                                    <p>Head admin</p>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                                <h6>13 aug 2022</h6>
                                                                                <p>Joined</p>
                                                                            </td>
                                                                            <td>
                                                                                <h6>213-456-2345</h6>
                                                                                <p>Phone no</p>
                                                                            </td>
                                                                            <td>
                                                                                <h6>New Yourk</h6>
                                                                                <p>Location</p>
                                                                            </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            5
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex">
                                                                                <img src="{{asset('assets/images/faces/face5.jpg')}}" alt="">
                                                                                <div>
                                                                                    <h6>Katherine Butler</h6>
                                                                                    <p>Head admin</p>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                                <h6>13 aug 2022</h6>
                                                                                <p>Joined</p>
                                                                            </td>
                                                                            <td>
                                                                                <h6>213-456-2345</h6>
                                                                                <p>Phone no</p>
                                                                            </td>
                                                                            <td>
                                                                                <h6>New Yourk</h6>
                                                                                <p>Location</p>
                                                                            </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
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
