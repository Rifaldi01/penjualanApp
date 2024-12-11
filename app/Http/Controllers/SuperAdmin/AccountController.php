<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cust = User::latest()->paginate();
        $title = 'Delete Data!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        $user = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin');
        })->get();

        return view('superadmin.account.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('superadmin.account.store'),
        ];

        if ($id) {
            $user = User::findOrFail($id);
            if ($user) {
                $inject = [
                    'url' => route('superadmin.account.update', $id),
                    'user' => $user,
                ];
            }
        }

        $role = Role::where('name', '!=', 'superadmin')->get();

        return view('superadmin.account.create', $inject, compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::create([
            'name'        => $request->input('name'),
            'email'       => $request->input('email'),
            'phone'       => $request->input('phone'),
            'image'       => $request->input('image'),
            'password' => Hash::make('123')

        ]);
        $user->assignRole($request->input('role'));
        return redirect()->route('superadmin.account.index')->withSuccess('Akun Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Add the logic for showing a single user's details, if necessary.
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->create($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->save($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->withSuccess('Account successfully deleted');
    }

    private function save(Request $request, $id = null)
    {
        $validate = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|unique:users,phone,' . $id,
        ]);

        $user = User::firstOrNew(['id' => $id]);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->image = $request->input('image');
        $user->save();
        $user->assignRole($request->input('role'));
        return redirect()->route('superadmin.account.index')->withSuccess('Account successfully saved');
    }
}
