<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesSale;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use RealRashid\SweetAlert\Facades\Alert;

class AccesoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $acces = Accessories::latest()->paginate();
        $title = 'Delete Item!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        $generator = new BarcodeGeneratorPNG(); // Inisialisasi generator barcode
        $acces = Accessories::latest()->paginate(10); // Mengambil data accessories terbaru dengan pagination
        $barcodes = []; // Array untuk menyimpan barcode

        foreach ($acces as $data) { // Loop melalui setiap accessories
            $barcode = base64_encode($generator->getBarcode($data->code_acces, $generator::TYPE_CODE_128)); // Generate barcode dan encode ke base64
            $barcodes[$data->id] = $barcode; // Simpan barcode di array dengan ID sebagai key
        }

        return view('manager.accessories.index', compact('acces', 'barcodes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('manager.acces.store')
        ];
        if ($id){
            $acces = Accessories::whereId($id)->first();
            $inject = [
                'url' => route('manager.acces.update', $id),
                'acces' => $acces
            ];
        }
        return view('manager.accessories.barcode', $inject);
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
        Accessories::whereId($id)->delete();
        Alert::success('Success', 'Delet Accessories Success');
        return back();
    }

    private function save(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|',
            'price' => 'required'
        ],[
            'name' => 'Nama Accessories Wajib Diisi',
            'price.required' => 'Price Accessories Wajib Diisi',
        ]);
        if ($id === null) {
            $codeAcces = time();
        }

        $acces = Accessories::firstOrNew(['id' => $id]);
        $acces->name = $request->input('name');
        $acces->price = $request->input('price');

        if ($id === null) {
            $acces->code_acces = $codeAcces;
        }

        $acces->save();
        Alert::success('Success', 'Save Data Success');
        return redirect()->route('manager.acces.index');
    }

    public function editmultiple()
    {
        $accessories = Accessories::all(); // Ambil semua accessories, atau sesuaikan dengan kebutuhan Anda
        return view('manager.accessories.edit', compact('accessories'));
    }
    public function checkCode(Request $request)
    {
        $code = $request->get('code_access');
        $accessory = Accessories::where('code_acces', $code)->first();

        if ($accessory) {
            return response()->json(['exists' => true, 'data' => $accessory]);
        } else {
            return response()->json(['exists' => false, 'message' => 'Code Accessories or No Seri not found!']);
        }
    }

    public function updateMultiple(Request $request)
    {
        // Validasi permintaan yang masuk
        $request->validate([
            'accessories' => 'required|array',
            'accessories.*.code_acces' => 'required|string',
            'accessories.*.stok' => 'required|integer|min:0',
        ]);

        $accessoriesData = $request->input('accessories');

        // Loop melalui setiap aksesori dan perbarui stoknya
        foreach ($accessoriesData as $accessoryData) {
            $accessory = Accessories::where('code_acces', $accessoryData['code_acces'])->first();

            if ($accessory) {
                $accessory->stok += $accessoryData['stok'];
                $accessory->save();
            }
        }

        // Kembalikan respons sukses
        return response()->json(['success' => true, 'message' => 'Accessories updated successfully!']);
    }
    public function download(Accessories $acces)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($acces->code_acces, $generator::TYPE_CODE_128);

        $fileName = 'barcode-' . $acces->code_acces . '.png';


        return Response($barcode, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }
    public function report()
    {
        $report = AccessoriesSale::with('sale', 'accessories')->get();
        return view('manager.report-acces.index', compact('report'));
    }
}
