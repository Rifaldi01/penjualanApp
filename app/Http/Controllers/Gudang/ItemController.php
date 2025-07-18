<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemIn;
use App\Models\ItemSale;
use App\Models\Pembelian;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Picqer\Barcode\BarcodeGeneratorHTML;
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
        $title = 'Delete Item!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $items = Item::whereHas('divisi', function ($query){
            $query->where('divisi_id', Auth::user()->divisi_id);
        })
        ->with('divisi')->get();
        $barcodes = [];

        foreach ($items as $item) {
            // Menghasilkan barcode untuk setiap item berdasarkan no_seri
            $barcode = base64_encode($generator->getBarcode($item->no_seri, $generator::TYPE_CODE_128));
            $barcodes[$item->id] = $barcode;
        }
        $setting = Setting::where('divisi_id', Auth::user()->divisi_id)->first();
        return view('gudang.item.index', compact('items', 'barcodes', 'setting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('gudang.item.store'),
            'divisi' => Divisi::pluck('name', 'id')->toArray(),
        ];

        if ($id) {
            $item = Item::whereId($id)->first();

            // Mencari kode_msk yang sesuai dengan no_seri di table item_ins
            $itemIn = ItemIn::where('no_seri', $item->no_seri)->first();

            $inject = [
                'url' => route('gudang.item.update', $id),
                'divisi' => Divisi::pluck('name', 'id')->toArray(),
                'item' => $item,
                'itemIn' => $itemIn // Masukkan itemIn untuk diakses di Blade
            ];
        }

        $cat = ItemCategory::all();
        return view('gudang.item.create', $inject, compact('cat'));
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
        // Cari item berdasarkan ID
        $item = Item::findOrFail($id);

        // Hapus semua item di tabel item_ins yang memiliki no_seri yang sama
        ItemIn::where('no_seri', $item->no_seri)->delete();

        // Hapus item dari tabel items
        $item->delete();

        // Tampilkan alert sukses

        return back()->withSuccess('Success', 'Delete Item Success');
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

        // Ambil divisi login
        $divisiId = Auth::user()->divisi_id;

        // Simpan atau update data di tabel `items`
        $item = Item::updateOrCreate(
            ['id' => $id],
            [
                'itemcategory_id' => $request->input('itemcategory_id'),
                'name' => $request->input('name'),
                'no_seri' => $request->input('no_seri'),
                'created_at' => $request->input('created_at'),
                'divisi_id' => $divisiId,
            ]
        );

        // Update atau buat data di tabel `item_ins`
        ItemIn::updateOrCreate(
            ['no_seri' => $item->no_seri],
            [
                'itemcategory_id' => $item->itemcategory_id,
                'divisi_id' => $item->divisi_id,
                'name' => $item->name,
                'created_at' => $item->created_at,
                'kode_msk' => $request->input('kode_msk'),
            ]
        );

        // Cek apakah invoice dengan divisi yang sama sudah ada di tabel `pembelian`
        $invoice = $request->input('kode_msk');

        $existingPembelian = Pembelian::where('invoice', $invoice)
            ->where('divisi_id', $divisiId)
            ->first();

        // Jika tidak ada, buat entri baru
        if (!$existingPembelian && $invoice) {
            Pembelian::create([
                'divisi_id' => $divisiId,
                'invoice' => $invoice,
                'status' => '1',
            ]);
        }

        Alert::success('Success', 'Data berhasil disimpan');
        return redirect()->route('gudang.item.index');
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

        return view('gudang.report-item.index', compact('report', 'categories', 'mostSoldCategories', 'leastSoldCategories', 'categorySalesCount'));
    }
    public function itemin()
    {
        $itemin = ItemIn::where('divisi_id', Auth::user()->divisi_id)->with('cat')->get();

        return view('gudang.item.itemin', compact('itemin'));
    }
    public function itemout()
    {
        $itemout = ItemSale::whereHas('sale.divisi', function ($query){
            $query->where('divisi_id', Auth::user()->divisi_id);
        })
            ->with('itemCategory')->get();
        return view('gudang.item.itemin', compact('itemout'));
    }
    public function print(Request $request)
    {
        // Ambil ID accessories yang dipilih dan jumlah barcode
        $selectedItems = $request->input('items', []);
        $barcodeQuantities = $request->input('barcode_quantity', []);

        // Validasi apakah ada accessories yang dipilih
        if (!$request->has('items') || !is_array($request->items)) {
            return redirect()->back()->withErrors(['error' => 'Pilih minimal 4 accessories untuk dicetak.']);
        }

        // Ambil data accessories yang dipilih
        $items = Item::whereIn('id', $request->items)->get();
        $setting = Setting::where('divisi_id', Auth::user()->divisi_id)->get();

        if ($items->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'Accessories yang dipilih tidak ditemukan.']);
        }

        // Hitung total jumlah barcode yang diminta
        $totalBarcodes = 0;
        foreach ($request->input('barcode_quantity', []) as $quantity) {
            $totalBarcodes += (int)$quantity;
        }

        // Validasi jumlah minimal barcode atau accessories
        if ($totalBarcodes < 4 && count($request->items) < 4) {
            return redirect()->back()->withErrors(['error' => 'Jika jumlah barcode kurang dari 4, maka minimal pilih 4 accessories.']);
        }


        // Ambil data accessories berdasarkan ID yang dipilih
        $barcodes = Item::whereIn('id', $selectedItems)
            ->select('id', 'no_seri', 'name')
            ->get();

        $generator = new BarcodeGeneratorHTML();
        $barcodePaths = [];
        $invalidBarcodes = [];

        // Generate barcode untuk masing-masing accessories
        foreach ($barcodes as $items) {
            $codeLength = strlen($items->no_seri);

            // Validasi panjang barcode


            $quantity = $barcodeQuantities[$items->id] ?? 1; // Default 1 jika tidak diisi
            $barcodePaths[$items->id] = [];
            for ($i = 0; $i < $quantity; $i++) {
                $barcodePaths[$items->id][] = $generator->getBarcode($items->no_seri, $generator::TYPE_CODE_128, 1.99, 30);
            }
        }

        // Jika ada barcode tidak valid, tampilkan pesan error


        // Render PDF
        $html = view('gudang.item.barcode-pdf', [
            'items' => $barcodes,
            'barcodePath' => $barcodePaths,
            'setting' => $setting,
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('barcode-item.pdf', ['Attachment' => false]);
    }
    public function reject($id)
    {
        // Update hanya item dengan ID yang diberikan dan status 0
        $item = Item::where('id', $id)->where('status', 0)->first();

        if ($item) {
            $item->update(['status' => 1]);
            return back()->with('success', 'Item berhasil direject.');
        }

        return back()->with('error', 'Item tidak ditemukan atau sudah direject.');
    }

    public function redy($id)
    {
        // Update hanya item dengan ID yang diberikan dan status 0
        $item = Item::where('id', $id)->where('status', 1)->first();

        if ($item) {
            $item->update(['status' => 0]);
            return back()->with('success', 'Item sudah redy.');
        }

        return back()->with('error', 'Item tidak ditemukan atau sudah direject.');
    }
    public function khusus($id)
    {
        // Update hanya item dengan ID yang diberikan dan status 0
        $item = Item::where('id', $id)->first();

        if ($item) {
            $item->update(['status' => 2]);
            return back()->with('success', 'Item Khusus.');
        }

        return back()->with('error', 'Item tidak ditemukan atau sudah direject.');
    }
    public function setting(Request $request)
    {
        $divisiId = Auth::user()->divisi_id;

        // Cek apakah sudah ada data untuk divisi ini
        $setting = Setting::where('divisi_id', $divisiId)->first();

        if ($setting) {
            // Jika sudah ada, update data
            $setting->update([
                'width' => $request->input('width'),
                'height' => $request->input('height'),
            ]);
        } else {
            // Jika belum ada, buat data baru
            Setting::create([
                'divisi_id' => $divisiId,
                'width' => $request->input('width'),
                'height' => $request->input('height'),
            ]);
        }

        return back()->withSuccess('Ukuran diatur');
    }


}
