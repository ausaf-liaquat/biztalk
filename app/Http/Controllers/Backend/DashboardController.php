<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        return view('Backend.pages.index');

    }
    public function userindex()
    {
        return view('Backend.pages.user-index');
    }

    public function userdata(Request $request)
    {
        if ($request->ajax()) {

            $data = User::doesntHave('roles')->latest()->get();
            return DataTables::of($data)
            // ->addIndexColumn()

                ->addColumn('user', function ($row) {

                    $user = "<img src='" . asset('uploads/avtars/' . $row->profile_image) . "' style='border-radius: 5px;' alt='image' />";
                    return $user;

                })
                ->addColumn('name', function ($row) {
                    $name = $row->first_name . ' ' . $row->last_name;
                    return $name;
                })
                ->addColumn('username', function ($row) {
                    $username = $row->username;
                    return $username;
                })
                ->addColumn('phone', function ($row) {
                    $phoneno = $row->phone_no;
                    return $phoneno;
                })
                ->addColumn('email', function ($row) {
                    $email = $row->email;
                    return $email;
                })
                ->addColumn('status', function ($row) {
                    if ($row->is_verified == "pending") {
                        $status = "<label class='badge badge-danger'>Pending</label>";
                    } elseif ($row->is_verified == "active") {
                        $status = "<label class='badge badge-success'>Active</label>";
                    }

                    return $status;
                })
                ->addColumn('joineddate', function ($row) {
                    $joindate = $row->created_at->format('d/m/Y');
                    return $joindate;

                })

                ->addColumn('action', function ($row) {

                    return $action = "<a class='btn btn-primary btn-rounded btn-icon action-btn' href='" . route('user.edit', ['id' => $row->id]) . "'>
                    Edit <i class='ti-pencil-alt btn-icon-append icons-table'></i></a>
                    <a class='btn btn-danger btn-rounded btn-icon action-btn'>
                    Delete <i class='ti-trash btn-icon-append icons-table'></i></a>
                    ";

                })

                ->rawColumns(['user', 'name', 'username', 'phone', 'email', 'status', 'joineddate', 'action'])
                ->make(true);

        }
    }
    public function useredit($id)
    {
        return view('Backend.pages.edit-user');
    }
}
