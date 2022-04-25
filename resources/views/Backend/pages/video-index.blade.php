@extends('Backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Videos Management</h4>
                        {{-- <p class="card-description">
                            Add class <code>.table-striped</code>
                        </p> --}}
                        <div class="table-responsive">

                            <table class="table table-striped" id="video_table">
                                <thead>
                                    <tr>
                                        <th>
                                            Video Title
                                        </th>
                                        <th style="width: 222px;">
                                            Video Description
                                        </th>
                                        <th>
                                            Video
                                        </th>
                                        <th>
                                            Posted by user
                                        </th>
                                        {{-- <th>
                                            Investment Required
                                        </th>
                                        <th>
                                            Category
                                        </th>
                                        <th>
                                            Is Video Approved?
                                        </th>
                                        <th>
                                            Is Video Flagged?
                                        </th>
                                       
                                        <th>
                                            Posted at
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
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">

                        <table class="table table-striped" id="video_table">
                            <thead>
                                <tr>
                                    <th>
                                        Investment Required
                                    </th>
                                    <th>
                                        Category
                                    </th>
                                    <th>
                                        Is Video Approved?
                                    </th>
                                    <th>
                                        Is Video Flagged?
                                    </th>
                                   
                                    <th>
                                        Posted at
                                    </th> 

                                    <th>
                                        Total Likes
                                    </th>
                                   
                                    <th>
                                        Total Comments
                                    </th> 
                                    <th>
                                        Total Views
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
@endsection
@section('extrajs')
    <script>
        $(document).ready(function() {

            var table = $('#video_table').DataTable({

                processing: true,
                serverSide: true,
                pageLength: 25,
                select: false,
                ajax: {
                    url: "{{ route('video.data') }}",

                },
                columns: [

                    {
                        data: 'video_title',
                        name: 'video_title',
                    },
                    {
                        data: 'video_description',
                        name: 'video_description',

                    },
                    {
                        data: 'video',
                        name: 'video',

                    },

                    // {
                    //     data: 'investment_req',
                    //     name: 'investment_req',

                    // },
                    // {
                    //     data: 'video_category',
                    //     name: 'video_category',

                    // },
                    // {
                    //     data: 'status',
                    //     name: 'status',

                    // },
                    // {
                    //     data: 'flagged_video',
                    //     name: 'flagged_video',

                    // },
                    {
                        data: 'user',
                        name: 'user',

                    },
                    // {
                    //     data: 'created_at',
                    //     name: 'created_at',

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
@endsection
