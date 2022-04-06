<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BizTalk</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/js/select.dataTables.min.css') }}"> --}}
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.3/css/select.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">

    <style type="text/css">
        i.icons {
            display: inline-block;
            font-size: 20px;
            width: 40px;
            text-align: left;
            color: #1F3BB3;
            vertical-align: middle;
        }

        .sidebar-dark .sidebar .nav .nav-item .nav-link .menu-title {
            font-size: 15px;
        }

        .navbar .navbar-menu-wrapper {
            background: #f2f2f2;
        }

        .sidebar-dark .navbar .navbar-brand-wrapper {
            background: #f2f2f2;
        }

        .sidebar-dark .navbar .navbar-brand-wrapper .navbar-toggler {
            color: #000000;
        }

        .dataTables_info {
            font-size: smaller;
            padding-top: 0px !important;
        }

        ul.pagination {
            margin-top: 1rem;
            font-size: 13px;
        }

        .page-item.active .page-link {
            background: linear-gradient(81deg, #1e2d7c, #87cafe);
            border-color: #1e2d7c;
        }

        .page-link {
            color: #1e2d7c;
            padding: .3rem .5rem;
            border-color: #1e2d7c;
        }

        .previous {
            margin-right: 10px;
            font-size: 10px;
        }

        .next {
            margin-left: 10px;
        }

        .pagination .page-item .page-link:focus {
            background: inherit !important;
            border: none;
            color: #1e2d7c;
            outline: none;
        }

        .page-item:first-child .page-link {
            border-color: #1e2d7c;
            border-top-left-radius: 3.25rem;
            border-bottom-left-radius: 3.25rem;
        }

        .page-item:last-child .page-link {
            border-color: #1e2d7c;
            border-top-right-radius: 3.25rem;
            border-bottom-right-radius: 3.25rem;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
            font-size: 17px;
        }

        .action-btn{
            padding: 1.1rem 1.1rem !important;
        }
        .icons-table{
            margin-right: 0px !important;
            vertical-align: middle;
        }
        .content-wrapper{
            padding: 1.5rem 1.5rem 1.5rem 1.5rem;
        }

    </style>

</head>

<body class="sidebar-dark">
    <div class="container-scroller">

        <!-- partial:partials/_navbar.html -->
        @include('Backend.partials.navbar')
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_settings-panel.html -->


            <!-- partial -->
            <!-- partial:partials/_sidebar.html -->
            @include('Backend.partials.sidebar')
            <!-- partial -->
            <div class="main-panel">
                @yield('content')

                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                @include('Backend.partials.footer')
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js') }}"></script>

    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ asset('assets/js/jquery.cookie.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/Chart.roundedBarCharts.js') }}"></script>
    <!-- End custom js for this page-->

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js">
    </script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    @yield('extrajs')
</body>

</html>
