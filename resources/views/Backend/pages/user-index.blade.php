@extends('Backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card card-rounded">
                    <div class="card-body">
                        <h4 class="card-title">Users Management</h4>
                        {{-- <p class="card-description">
                            Add class <code>.table-striped</code>
                        </p> --}}
                        <div class="table-responsive mt-1">
                            <table class="table table-striped select-table" id="user_table">
                                <thead>
                                    <tr>
                                        <th>
                                            User
                                        </th>
                                        <th>
                                            Name
                                        </th>
                                        <th>
                                            Username
                                        </th>

                                        <th>
                                            Email
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                        {{-- <th>
                                            Joined date
                                        </th> --}}

                                        <th>
                                            Actions
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">User details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive mt-1">

                        <table class="table table-striped select-table" id="video_table">

                            <thead>
                                <tr>
                                    <th>
                                        Phone no
                                    </th>
                                    <th>
                                        Is Account Public
                                    </th>
                                    <th>
                                        Country
                                    </th>
                                    <th>
                                        Total Followers
                                    </th>
                                    <th>
                                        Total Followings
                                    </th>

                                    <th>
                                        Total Likes Received
                                    </th>

                                    <th>
                                        DOB
                                    </th>

                                    <th>
                                        Gender
                                    </th>

                                </tr>
                            </thead>
                            <div class="spinner" id="loader">
                                {{-- <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div> --}}
                                <img src="{{ asset('assets/images/loader.gif') }}" alt="" srcset="">
                            </div>
                            <tbody id="tbody">


                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('extrajs')
    <script>
        function viewdetails(e) {
            var id = $(e).data("id");
            var $loading = $('#loader');
            var url = '{{ route('user.details', ':id') }}';
            url = url.replace(':id', id);

            $.ajax({
                type: "GET",
                url: url,
                dataType: 'json',
                // beforeSend: function() { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                //     $loading.show();
                // },
                success: function(data) {
                    // On Success, build our rich list up and append it to the #richList div.
                    $("#tbody").empty();


                    $("#tbody").append("<tr><td>" + data.user_details.phone +
                        "</td><td>" + data.user_details.isaccount_public +
                        "</td><td>" + data.user_details.country +
                        "</td><td>" + data.user_details.followers_count +
                        "</td><td>" + data.user_details.followings_count +
                        "</td><td>" + data.user_details.total_like_received +
                        "</td><td>" + data.user_details.dob +
                        "</td><td>" + data.user_details.gender +
                        "</td></tr>");


                    $('#exampleModal').modal('show');
                },
                complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $loading.hide();
                },
            });


        }
    </script>
    <script>
        $(document).ready(function() {

            var table = $('#user_table').DataTable({

                processing: true,
                serverSide: true,
                pageLength: 25,
                select: false,
                ajax: {
                    url: "{{ route('user.data') }}",

                },
                columns: [

                    {
                        data: 'user',
                        name: 'user',
                    },
                    {
                        data: 'name',
                        name: 'name',

                    },
                    {
                        data: 'username',
                        name: 'username',

                    },
                    {
                        data: 'email',
                        name: 'email',

                    },
                    {
                        data: 'status',
                        name: 'status',

                    },
                    // {
                    //     data: 'joineddate',
                    //     name: 'joineddate',

                    // },
                    {
                        data: 'action',
                        name: 'action',

                    },

                ],
                'responsive': false,
                "ordering": false,
                "lengthChange": false,
                "pageLength": 10,


            });
            $('#table_id_search').keyup(function() {
                table.search($(this).val()).draw();
            })
        });
    </script>
    <script>
        function deleteUser(e) {
            // e.preventDefault();

            var id = $(e).data("id");


            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover",
                icon: "warning",
                showCancelButton: true,

                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "GET",
                        url: '{{ route('user.delete') }}',
                        data: {
                            id: id
                        },
                        success: function(data) {
                            if ($.isEmptyObject(data.error)) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'User deleted successfully',
                                    showConfirmButton: true,
                                    timer: 2500
                                }).then((result) => {
                                    // Reload the Page
                                    location.reload();
                                });
                            }
                        }
                    });

                }
            });


        };
    </script>
@endsection
