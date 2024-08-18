<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::all();
        return view('profile.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('profile.edit.store'),
        ];
        if ($id) {
            $user = User::findOrFail($id);
            $inject = [
                'url' => route('profile.edit.update', $id),
                'user' => $user
            ];
        }
        return view('profile.index', $inject);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->save($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return $this->create($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return $this->save($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function save(Request $request, $id = null)
    {
        // Adjust validation rules: make fields required only if $id is null (i.e., on creation)
        $validate = $request->validate([
            'name'    => $id ? 'nullable|string|max:255' : 'required|string|max:255',
            'email'   => $id ? 'nullable|email|max:255' : 'required|email|max:255|unique:users,email,' . $id,
            'password'=> $id ? 'nullable|min:8' : 'required|min:8',
            'phone'   => $id ? 'nullable|string|min:10' : 'required|string|min:10',
            'image'   => $id ? 'nullable|image' : 'required|image',
        ]);

        $user = User::firstOrNew(['id' => $id]);

        if ($request->has('name') && $request->input('name') != null) {
            $user->name = $request->input('name');
        }

        if ($request->has('email') && $request->input('email') != null) {
            $user->email = $request->input('email');
        }

        if ($request->has('password') && $request->input('password') != null) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->has('phone') && $request->input('phone') != null) {
            $user->phone = $request->input('phone');
        }

        if ($request->hasFile('image')) {
            if ($user->image && file_exists(public_path('images/profile/' . $user->image))) {
                unlink(public_path('images/profile/' . $user->image));
            }
            $file = $request->file('image');
            $file_name = md5(now()) . '.' . $file->getClientOriginalExtension();
            $file->move('images/profile/', $file_name);
            $user->image = $file_name;
        }

        $user->save();


        return redirect()->back()->withSuccess('Profile Berhasil Diperbarui');
    }
}
