<?php

namespace App\Http\Controllers\manager;

use Dompdf\Dompdf;
use App\Models\Item;
use App\Models\Divisi;
use App\Models\ItemIn;
use App\Models\ItemSale;
use Milon\Barcode\DNS1D;
use App\Models\Pembelian;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;
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

        $title = 'Delete Item!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $items = Item::all();
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
        $divisi = Divisi::all();
        return view('manager.item.create', $inject, compact('cat', 'divisi'));
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
        $item = Item::findOrFail($id);

        // Hapus semua item di tabel item_ins yang memiliki no_seri yang sama
        ItemIn::where('no_seri', $item->no_seri)->delete();

        // Hapus item dari tabel items
        $item->delete();
        Alert::success('Success', 'Delet Iteme Success');
        return back();
    }
    private function save(Request $request, $id = null)
    {
        $validate = $request->validate([
            'name' => 'required',
            'itemcategory_id' => 'required',
            'no_seri' => $id ? 'required' : 'required|unique:items',
        ], [
            'name.required' => 'Nama Tidak Boleh Kosong',
            'itemcategory_id.required' => 'Pilih Category',
            'no_seri.required' => 'Nomor Seri Tidak Boleh Kosong',
            'no_seri.unique' => 'Nomor Seri Sudah Terdaftar',
        ]);

        // Simpan atau update data di tabel `items`
        $item = Item::updateOrCreate(
            ['id' => $id],
            [
                'itemcategory_id' => $request->input('itemcategory_id'),
                'name' => $request->input('name'),
                'no_seri' => $request->input('no_seri'),
                'created_at' => $request->input('created_at'),
                'price' => $request->input('price'),
                'capital_price' => $request->input('capital_price'),
                'divisi_id' => $request->input('divisi_id'),
            ]
        );

        // Update atau buat data di tabel `item_ins`
        ItemIn::updateOrCreate(
            ['no_seri' => $item->no_seri], // Cari berdasarkan `no_seri`
            [
                'itemcategory_id' => $item->itemcategory_id,
                'divisi_id' => $item->divisi_id,
                'name' => $item->name,
                'price' => $item->price,
                'capital_price' => $item->capital_price,
                'created_at' => $item->created_at,
                'kode_msk' => $request->input('kode_msk'),
            ]
        );

        // Cek apakah invoice sudah ada di tabel `pembelian`
        $invoice = $request->input('kode_msk');
        $existingPembelian = Pembelian::where('invoice', $invoice)->first();

        // Jika invoice belum ada, buat entri baru di tabel `pembelian`
        if (!$existingPembelian && $invoice) {
            Pembelian::create([
                'divisi_id' => $item->divisi_id,
                'invoice' => $invoice,
                'status' => '1',
            ]);
        }

        return redirect()->route('manager.item.index')->withSuccess('Data berhasil disimpan');
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
    public function itemin()
    {
        $items = Item::with('cat', 'divisi')->get();
        $itemSales = ItemSale::with('itemCategory', 'divisi')->get();

        // Menggabungkan kedua koleksi menjadi satu
        $itemin = $items->merge($itemSales);

        return view('manager.item.itemin', compact('itemin'));
    }
    public function itemout()
    {
        $itemout = ItemSale::with('itemCategory')->get();
        return view('manager.item.itemin', compact('itemout'));
    }
    public function print(Request $request)
    {
        // Ambil ID Items yang dipilih dan jumlah barcode
        $selectedItems = $request->input('items', []);
        $barcodeQuantities = $request->input('barcode_quantity', []);

        // Validasi apakah ada Items yang dipilih
        if (!$request->has('items') || !is_array($request->items)) {
            return redirect()->back()->withErrors(['error' => 'Pilih minimal 4 Items untuk dicetak.']);
        }

        // Ambil data Items yang dipilih
        $items = Item::whereIn('id', $request->items)->get();

        if ($items->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'Items yang dipilih tidak ditemukan.']);
        }

        // Hitung total jumlah barcode yang diminta
        $totalBarcodes = 0;
        foreach ($request->input('barcode_quantity', []) as $quantity) {
            $totalBarcodes += (int)$quantity;
        }

        // Validasi jumlah minimal barcode atau Items
        if ($totalBarcodes < 4 && count($request->items) < 4) {
            return redirect()->back()->withErrors(['error' => 'Jika jumlah barcode kurang dari 4, maka minimal pilih 4 Items.']);
        }


        // Ambil data Items berdasarkan ID yang dipilih
        $barcodes = Item::whereIn('id', $selectedItems)
            ->select('id', 'no_seri', 'name')
            ->get();

        $generator = new BarcodeGeneratorHTML();
        $barcodePaths = [];
        $invalidBarcodes = [];

        // Generate barcode untuk masing-masing Items
        foreach ($barcodes as $items) {
            $codeLength = strlen($items->no_seri);

            // Validasi panjang barcode


            $quantity = $barcodeQuantities[$items->id] ?? 1; // Default 1 jika tidak diisi
            $barcodePaths[$items->id] = [];
            for ($i = 0; $i < $quantity; $i++) {
                $barcodePaths[$items->id][] = $generator->getBarcode($items->no_seri, $generator::TYPE_CODE_128, );
            }
        }

        // Jika ada barcode tidak valid, tampilkan pesan error


        // Render PDF
        $html = view('manager.item.barcode-pdf', [
            'items' => $barcodes,
            'barcodePath' => $barcodePaths,
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('barcode-item.pdf', ['Attachment' => false]);
    }

}
