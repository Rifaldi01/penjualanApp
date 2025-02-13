<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cust = Customer::latest()->paginate();
        $title = 'Delete Data!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        $cust = Customer::all();
        return view('manager.customer.index', compact('cust'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('manager.customer.store'),
            'divisi'=> Divisi::pluck('name', 'id')->toArray(),

        ];
        if ($id){
            $cust = Customer::whereId($id)->first();
            $inject = [
                'url' => route('manager.customer.update', $id),
                'divisi'=> Divisi::pluck('name', 'id')->toArray(),
                'cust' => $cust
            ];
        }
        return view('manager.customer.create', $inject);
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
        $customer = Customer::findOrFail($id);
        $sale = $customer->sale()->with('customer', 'itemSales.itemCategory', 'accessoriesSales.accessories')->get();
        //return $sale;
        return view('manager.customer.show', compact('customer', 'sale'));
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
        Customer::whereId($id)->delete();
        Alert::success('Success', 'Delete Customer Success');
        return back();
    }

    private function save(Request $request, $id = null)
    {
        $validate = $request->validate([
            'name' => 'required',
            'phone_wa' => $id ? 'nullable|numeric' : 'required|regex:/^\d{9}$/|unique:customers|numeric',
            'addres' => 'required'
        ],[
            'name.required' => 'Name Customer Tidak Boleh Kosong',
            'phone_wa.required' => 'Phone Whatsapp Tidak Boleh Kosong',
            'phone_wa.regex' => 'Phone Whatsapp Harus 12 Angka',
            'phone_wa.unique' => 'Phone Whatsapp Sudah Terdaftar',
            'phone_wa.numeric' => 'Phone Whatsapp Harus Angka',
            'addres.required' => 'Address Tidak Boleh Kosong',
        ]);
        $cust = Customer::firstOrNew(['id' => $id]);
        $cust->name = $request->input('name');
        $cust->phone_wa = $request->input('phone_wa');
        $cust->phone = $request->input('phone');
        $cust->addres = $request->input('addres');
        $cust->divisi_id = $request->input('divisi_id');
        $cust->save();
        Alert::success('Success', 'Save Data Success');
        return redirect()->route('manager.customer.index');
    }

    public function import(Request $request)
    {
        $customer = $request->file('file');

        $namefile = $customer->getClientOriginalName();
        $customer->move('file/customer/', $namefile);
        Excel::import(new CustomerImport, public_path('file/customer/'. $namefile));
        return redirect()->back()->withSuccess('Import Data Success');
    }
}
