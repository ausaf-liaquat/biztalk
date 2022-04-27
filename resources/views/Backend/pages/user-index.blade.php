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
                                            Phone no
                                        </th>
                                        <th>
                                            Email
                                        </th>

                                        <th>
                                            Status
                                        </th>

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
@endsection

@section('extrajs')
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
                        data: 'phone',
                        name: 'phone',

                    },
                    {
                        data: 'email',
                        name: 'email',

                    },
                    {
                        data: 'joineddate',
                        name: 'joineddate',

                    },
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
