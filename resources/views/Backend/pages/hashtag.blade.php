@extends('Backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Hashtags</h4>
                        {{-- <p class="card-description">
                            Add class <code>.table-striped</code>
                        </p> --}}
                        <div class="table-responsive">

                            <table class="table table-striped" id="hashtag_table">
                                <thead>
                                    <tr>
                                        <th>
                                            Sr. no
                                        </th>
                                        <th>
                                            Hashtag Title
                                        </th>
                                        <th>
                                            Total Videos
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
    </div>
@endsection
@section('extrajs')
    <script>
        $(document).ready(function() {

            var table = $('#hashtag_table').DataTable({

                processing: true,
                serverSide: true,
                pageLength: 25,
                select: false,
                ajax: {
                    url: "{{ route('hashtags.data') }}",

                },
                columns: [

                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'hashtag_title',
                        name: 'hashtag_title',
                    },
                    {
                        data: 'total_videos',
                        name: 'total_videos',

                    },
                    {
                        data: 'total_views',
                        name: 'total_views',

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
