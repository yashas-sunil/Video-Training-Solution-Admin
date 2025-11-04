<?php

namespace App\Http\Controllers;

use App\Mail\AdminRolesMail;
use App\Mail\ProfessorRegisterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\User;
use App\Role;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            $query = User::with(['roleRelation' => function ($q) {
                $q->select('id', 'name');
            }])
                ->orderByDesc('id');

            return DataTables::of($query)
                ->addColumn('action', 'pages.admins.action')
                ->editColumn('phone', function ($query) {
                    if ($query->phone) {
                        return $query->country_code . ' ' . $query->phone;
                    }
                    return '-';
                })
                ->addColumn('role', function ($query) {
                    return $query->roleRelation ? $query->roleRelation->name : 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $html = $builder->columns([
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'phone', 'name' => 'phone', 'title' => 'Mobile'],
            ['data' => 'role', 'name' => 'roleRelation.name', 'title' => 'Role'],
            ['data' => 'action', 'name' => 'action', 'title' => '', 'searchable' => false, 'orderable' => false, 'width' => '10%']
        ]);

        return view('pages.admins.index', compact('html'));
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name', 'id');
        return view('pages.admins.create', compact('roles'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|alpha_spaces',
            'email' => 'required|email|unique:users',
            'mobile' => [
                'required',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:9',
                'max:10',
                Rule::unique('users', 'phone')->whereNotNull('phone'),
            ],
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6'
        ]);
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->country_code = $request->mobile_code;
        $admin->phone = $request->mobile;
        $admin->password = Hash::make($request->password);
        $admin->role = $request->role_id;

        $user_details = [
            'name' => $admin->name,
            'email' => $admin->email,
            'password' => $request->password,
            'phone' => $admin->phone,
        ];

        try {
            Mail::send(new AdminRolesMail($user_details));
        } catch (\Exception $exception) {
            info($exception->getMessage(), ['exception' => $exception]);
        }

        $admin->save();

        return redirect(route('admins.index'))->with('success', 'Admin successfully created');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        $roles = Role::select('id', 'name')->get();

        return view('pages.admins.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/'], // alpha_spaces ka alt
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile' => 'nullable|regex:/^[0-9]{10}$/',
            'role' => 'required|exists:roles,id',
        ]);

        $admin = Admin::findOrFail($id);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->country_code = $request->mobile_code;
        $admin->phone = $request->mobile;
        $admin->role = $request->role;
        $admin->save();

        return redirect()
            ->route('admins.index')
            ->with('success', 'Admin successfully updated');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /** @var Admin $admin */
        $admin = Admin::findOrFail($id);

        $admin->delete();

        return response()->json(true, 200);
    }

    public function validatePhone()
    {
        $phoneExists = User::query()->where('role', 5)->where('phone', request('mobile'))->exists();

        if ($phoneExists) {
            return 'false';
        }

        return 'true';
    }
    public function toggleStatus($id)
    {
        $admin = Admin::findOrFail($id);

        $admin->status = $admin->status === 'active' ? 'blocked' : 'active';
        $admin->save();

        return response()->json([
            'success' => true,
            'status' => $admin->status,
            'message' => 'User status updated successfully!'
        ]);
    }
}
