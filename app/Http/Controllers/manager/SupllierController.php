<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
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
        $divisiUser = Divisi::where('name')->get();
        $divisi = Divisi::all();

        if ($request->ajax()) {
            $query = Supplier::query();

            // Filter berdasarkan divisi jika ada
            if ($request->filled('divisi_id')) {
                $query->where('divisi_id', $request->divisi_id);
            }

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="editSupplier btn btn-warning btn-sm bx bx-edit" data-id="'.$row->id.'"></a>';
                    $btn .= ' <a href="javascript:void(0)" class="deleteSupplier btn btn-danger btn-sm bx bx-trash" data-id="'.$row->id.'"></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('manager.supplier.index', compact('divisi', 'divisiUser'));
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
            'divisi_id' => 'required|exists:divisis,id',
            'name' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|unique:suppliers|regex:/^[0-9]+$/',
        ]);

        Supplier::create($request->all());

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
            'divisi_id' => 'required|exists:divisis,id',
            'name' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|regex:/^[0-9]+$/',
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
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['success' => 'Supplier deleted successfully!']);
    }
}
