@extends('Backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card card-rounded">
                    <div class="card-body">

                        <div class="d-sm-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="card-title">Community Guidelines</h4>
                                <p class="card-description">
                                    Add Guidelines and privacies
                                </p>
                            </div>
                            <div>
                                {{-- <button class="btn btn-primary text-white mb-0 me-0" data-bs-toggle='modal'
                                    data-bs-target='#exampleModal' type="button"><i
                                        class="fa-solid fa-square-plus menu-icon"></i> Add
                                    new Community Guidelines</button> --}}
                            </div>
                        </div>
                        <div class="table-responsive mt-1">
                            <table class="table table-striped select-table">
                                <thead>
                                    <tr>
                                        <th>
                                            Sr. no
                                        </th>
                                        <th>
                                            tos
                                        </th>
                                        {{-- <th>
                                            privacy
                                        </th> --}}
                                        <th>
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($community as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td style="white-space: pre-wrap;text-align:justify;"> {{ $item->tos }}</td>
                                            {{-- <td style="white-space: pre-wrap;text-align:justify;">{{ $item->privacy }}
                                            </td> --}}
                                            <td>
                                                <a class='btn btn-primary btn-icon' href='{{ route('editcommunity.index',['id'=>$item->id]) }}'>
                                                    Edit <i class='ti-pencil-alt btn-icon-append icons-table'></i></a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
