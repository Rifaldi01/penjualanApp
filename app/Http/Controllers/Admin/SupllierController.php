<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class SupllierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $suppliers = Supplier::where('divisi_id', Auth::user()->divisi_id)->get();
            return Datatables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="editSupplier btn btn-warning btn-sm bx bx-edit" data-id="'.$row->id.'"></a>';
                    $btn .= ' <a href="javascript:void(0)" class="deleteSupplier btn btn-danger btn-sm bx bx-trash" data-id="'.$row->id.'"></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.supplier.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'kode' => 'required|unique:suppliers',
            'name' => 'required',
            'alamat' => 'required',
            'telepon' => 'required|unique:suppliers',
        ]);
        // Ambil divisi_id dari user yang sedang login
        $divisiId = auth()->user()->divisi_id;

        // Tambahkan divisi_id ke data yang akan disimpan
        $data = $request->all();
        $data['divisi_id'] = $divisiId;

        // Simpan data supplier
        Supplier::create($data);

        return response()->json(['success' => 'Supplier added successfully!']);
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
        $supplier = Supplier::findOrFail($id);
        return response()->json($supplier);
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
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'kode' => 'required|unique:suppliers,kode,' . $supplier->id,
            'name' => 'required',
            'alamat' => 'required',
            'telepon' => 'required',
        ]);

        $supplier->update($request->all());
        return response()->json(['success' => 'Supplier updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();
        return response()->json(['success' => 'Supplier deleted successfully!']);
    }
}
