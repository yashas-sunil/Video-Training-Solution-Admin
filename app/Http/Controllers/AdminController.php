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
            $query = Admin::whereIn('role', [
                User::ROLE_ADMIN,
                User::ROLE_COURSE_ADMIN,
                User::ROLE_BUSINESS_ADMIN,
                User::ROLE_PLATFORM_ADMIN,
                User::ROLE_REPORT_ADMIN,
                User::ROLE_CONTENT_MANAGER,
                User::ROLE_FINANCE_MANAGER,
                User::ROLE_BRANCH_MANAGER,
                User::ROLE_ASSISTANT,
                User::ROLE_REPORTING,
                User::ROLE_BACKOFFICE_MANAGER,
                User::ROLE_JUNIOR_ADMIN,

            ]);

            return DataTables::of($query)
                ->addColumn('action', 'pages.admins.action')
                ->editColumn('phone', function ($query) {
                    if ($query->phone) {
                        return $query->country_code . ' ' . $query->phone;
                    }
                })
                ->addColumn('role', function ($query) {
                    switch ($query->role) {
                        case User::ROLE_ADMIN:
                            return User::ROLE_ADMIN_TEXT;
                            break;
                        case User::ROLE_COURSE_ADMIN:
                            return User::ROLE_COURSE_ADMIN_TEXT;
                            break;
                        case User::ROLE_BUSINESS_ADMIN:
                            return User::ROLE_BUSINESS_ADMIN_TEXT;
                            break;
                        case User::ROLE_PLATFORM_ADMIN:
                            return User::ROLE_PLATFORM_ADMIN_TEXT;
                            break;
                        case User::ROLE_REPORT_ADMIN:
                            return User::ROLE_REPORT_ADMIN_TEXT;
                            break;
                        case User::ROLE_CONTENT_MANAGER:
                            return User::ROLE_CONTENT_MANAGER_TEXT;
                            break;
                        case User::ROLE_FINANCE_MANAGER:
                            return User::ROLE_FINANCE_MANAGER_TEXT;
                            break;
                        case User::ROLE_BRANCH_MANAGER:
                            return User::ROLE_BRANCH_MANAGER_TEXT;
                            break;
                        case User::ROLE_ASSISTANT:
                            return User::ROLE_ASSISTANT_TEXT;
                            break;
                        case User::ROLE_REPORTING:
                            return User::ROLE_REPORTING_TEXT;
                            break;
                        case User::ROLE_BACKOFFICE_MANAGER:
                            return User::ROLE_BACKOFFICE_MANAGER_TEXT;
                            break;
                        case User::ROLE_JUNIOR_ADMIN:
                            return User::ROLE_JUNIOR_ADMIN_TEXT;
                            break;
                        default:
                            return 'Unknown';
                            break;
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        //dd($data);
        $html = $builder->columns([
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'phone', 'name' => 'phone', 'title' => 'Mobile'],
            ['data' => 'role', 'name' => 'role', 'title' => 'Role'],
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
        return view('pages.admins.create');
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
            'mobile' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|max:10|min:9',
            'role' => 'required',
            'password' => 'required|min:6'
        ]);
        //dd($request->all());

        $admin = new Admin();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->country_code = $request->mobile_code;
        $admin->phone = $request->mobile;
        $admin->password = Hash::make($request->password);
        $admin->role = $request->role;
        //dd($admin->all());
        $user_details['name'] = $admin->name;
        $user_details['email'] = $admin->email;
        $user_details['password'] = $request->password;
        $user_details['phone'] = $admin->phone;

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
        /** @var Admin $admin */
        $admin = Admin::findOrFail($id);

        return view('pages.admins.edit', compact('admin'));
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
            'name' => 'required|alpha_spaces',
            'email' =>  'required|unique:users,email,' . $id,
            'mobile' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|max:10|min:9',
            'role' => 'required',
        ]);

        /** @var Admin $admin */
        $admin = Admin::findOrFail($id);

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->country_code = $request->mobile_code;
        $admin->phone = $request->mobile;
        //        $admin->password = Hash::make(Str::random(8));
        $admin->role = $request->role;

        $admin->save();

        return redirect(route('admins.index'))->with('success', 'Admin successfully updated');
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
