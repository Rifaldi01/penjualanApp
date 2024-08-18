<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemSale;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use RealRashid\SweetAlert\Facades\Alert;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cust = Item::latest()->paginate();
        $title = 'Delete Item!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $items = Item::latest()->paginate();
        $barcodes = [];

        foreach ($items as $item) {
            // Menghasilkan barcode untuk setiap item berdasarkan no_seri
            $barcode = base64_encode($generator->getBarcode($item->no_seri, $generator::TYPE_CODE_128));
            $barcodes[$item->id] = $barcode;
        }
        return view('manager.item.index', compact('items', 'barcodes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('manager.item.store'),
        ];
        if ($id){
            $item = Item::whereId($id)->first();
            $inject = [
                'url' => route('manager.item.update', $id),
                'item' => $item
            ];
        }
        $cat = ItemCategory::all();
        return view('manager.item.create', $inject, compact('cat'));
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
        Item::whereId($id)->delete();
        Alert::success('Success', 'Delet Iteme Success');
        return back();
    }
    private function save(Request $request, $id = null)
    {
        $validate = $request->validate([
            'name'=> 'required',
            'itemcategory_id'=> 'required',
            'no_seri'=> $id ? 'required' : 'required|unique:items',
            'price' => 'required'
        ],[
            'name.required' => 'Nama Tidak Boleh Kosong',
            'itemcategory_id.required' => 'Pilih Category',
            'no_seri.required' => 'Nomor Seri Tidak Boleh Kosong',
            'no_seri.unique' => 'Nomor Seri Sudah Terdaftar',
            'price.required' => 'Harga Tidak boleh Kosong',
        ]);
        $item = Item::firstOrNew(['id' => $id]);
        $item->itemcategory_id  = $request->input('itemcategory_id');
        $item->name    = $request->input('name');
        $item->no_seri = $request->input('no_seri');
        $item->price = $request->input('price');
        $item->save();
        Alert::success('Success', 'Upload Data Success');
        return redirect()->route('manager.item.index');
    }

    public function download(Item $item)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($item->no_seri, $generator::TYPE_CODE_128);

        $fileName = 'barcode-' . $item->no_seri . '.png';


        return Response($barcode, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    public function report()
    {
        // Ambil data item yang terjual
        $report = ItemSale::with('itemCategory')->get();

        // Ambil semua kategori
        $categories = ItemCategory::all();

        // Hitung frekuensi penjualan per kategori
        $categorySalesCount = $categories->mapWithKeys(function ($category) use ($report) {
            // Hitung jumlah penjualan untuk setiap kategori
            $count = $report->filter(function ($sale) use ($category) {
                return $sale->itemCategory->id == $category->id;
            })->count();

            return [$category->name => $count];
        });

        // Temukan kategori yang sering terjual dan jarang terjual
        $mostSoldCategories = $categorySalesCount->sortDesc()->take(5); // 5 kategori paling sering terjual
        $leastSoldCategories = $categorySalesCount->sort()->take(5); // 5 kategori paling jarang terjual

        return view('manager.report-item.index', compact('report', 'categories', 'mostSoldCategories', 'leastSoldCategories', 'categorySalesCount'));
    }
}
