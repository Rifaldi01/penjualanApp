<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $divisi = Divisi::all();
        return view('superadmin.divisi.index', compact('divisi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('superadmin.divisi.store')
        ];
        if ($id){
            $divisi = Divisi::whereId($id)->first();
            $inject = [
                'url' => route('superadmin.divisi.update', $id),
                'divisi' => $divisi
            ];
        }
        return view('superadmin.divisi.create', $inject);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->save($request);
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
        return $this->create($id);
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
        return $this->save($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    private function save(Request $request, $id = null)
    {
        $validate = $request->validate([
            'name' => 'required',
            'no_rek' => 'required',
            'kode' => 'required|unique:divisis,kode,' . $id,
            'inv_format' => 'required|unique:divisis,inv_format,' . $id,
            'email' => 'unique:divisis,email,' . $id,
            'logo' => $id ? 'nullable|image' : 'required',
        ]);

        $divisi = Divisi::firstOrNew(['id' => $id]);
        $divisi->name = $request->input('name');
        $divisi->kode = $request->input('kode');
        $divisi->no_rek = $request->input('no_rek');
        $divisi->inv_format = $request->input('inv_format');
        $divisi->alamat = $request->input('alamat');
        $divisi->phone = $request->input('phone');
        $divisi->email = $request->input('email');

        if ($request->hasFile('logo')) {
            if ($divisi->logo && file_exists(public_path('images/logo/' . $divisi->logo))) {
                unlink(public_path('images/logo/' . $divisi->logo));
            }

            $file = $request->file('logo');
            $file_name = md5(now()) . '.png';

            $img = ImageManagerStatic::make($file);
            $img = $img->resize(null, 600, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save(public_path("images/logo/{$file_name}"), 50, 'png');

            $divisi->logo = $file_name;
        }

        $divisi->save();
        return redirect()->route('superadmin.divisi.index')->withSuccess('Divisi successfully Added');
    }
}
