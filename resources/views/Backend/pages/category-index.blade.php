@extends('Backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card card-rounded">
                    <div class="card-body">

                        <div class="d-sm-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="card-title">Category Management</h4>
                                <p class="card-description">
                                    Add list of Price range for investment
                                </p>
                            </div>
                            <div>
                                <button class="btn btn-primary text-white mb-0 me-0" data-bs-toggle='modal'
                                    data-bs-target='#exampleModal' type="button"><i
                                        class="fa-solid fa-square-plus menu-icon"></i> Add
                                    new categories</button>
                            </div>
                        </div>
                        <div class="table-responsive mt-1">
                            <table class="table table-striped select-table" id="category_table">
                                <thead>
                                    <tr>
                                        <th>
                                            Sr. no
                                        </th>
                                        <th>
                                            Name
                                        </th>
                                        <th>
                                            Total Videos
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
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" action="{{ route('category.store') }}" method="post" autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" class="form-control" onkeyup="duplicateCategory(this)" id="category"
                                placeholder="Price range e.g (0000 - 9999)" name="category" value="" required>
                                <span class="text-danger mb-4 email-error" id="category-error"
                                style="font-size: small;"></span>
                        </div>


                        <button type="submit" class="btn btn-primary me-2" id="submit">Submit</button>

                    </form>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('extrajs')
    <script>
        $(document).ready(function() {

            var table = $('#category_table').DataTable({

                processing: true,
                serverSide: true,
                pageLength: 25,
                select: false,
                ajax: {
                    url: "{{ route('category.data') }}",

                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name',

                    },
                    {
                        data: 'total_videos',
                        name: 'total_videos',

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
        function deletecategory(e) {
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
                        url: '{{ route('category.delete') }}',
                        data: {
                            id: id
                        },
                        success: function(data) {
                            if ($.isEmptyObject(data.error)) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Category deleted successfully',
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
    <script>
         $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function duplicateCategory(element) {
            var category = $(element).val();
            $.ajax({
                type: "POST",
                url: "{{ route('category.duplicate') }}",
                data: {
                    category: category
                },
                dataType: "json",
                success: function(res) {
                    if (res.exists) {
                        $('#category-error').html("Category must be unique!");
                        $('#category-error')
                            .prop('hidden', false);
                        $('#submit').prop('disabled', true);

                    } else {

                        $('#category-error')
                            .prop('hidden', true);
                        $('#submit').prop('disabled', false);

                    }
                },
                error: function(jqXHR, exception) {

                }
            });
        }
    </script>
@endsection
