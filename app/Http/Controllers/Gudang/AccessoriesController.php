<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\AccessoriesIn;
use App\Models\AccessoriesRejecte;
use App\Models\AccessoriesSale;
use App\Models\Divisi;
use App\Models\Item;
use App\Models\Pembelian;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;
use RealRashid\SweetAlert\Facades\Alert;

class AccessoriesController extends Controller
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
        $generator = new BarcodeGeneratorPNG(); // Inisialisasi generator barcode
        $acces = Accessories::whereHas('divisi', function ($query){
            $query->where('divisi_id', Auth::user()->divisi_id);
        })
            ->with('divisi')
            ->get();// Mengambil data accessories terbaru dengan pagination
        $barcodes = []; // Array untuk menyimpan barcode

        foreach ($acces as $data) { // Loop melalui setiap accessories
            $barcode = base64_encode($generator->getBarcode($data->code_acces, $generator::TYPE_CODE_128)); // Generate barcode dan encode ke base64
            $barcodes[$data->id] = $barcode; // Simpan barcode di array dengan ID sebagai key
        }

        return view('gudang.accessories.index', compact('acces', 'barcodes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $inject = [
            'url' => route('gudang.acces.store'),
            'divisi' => Divisi::pluck('name', 'id')->toArray(),
        ];
        if ($id){
            $acces = Accessories::whereId($id)->first();
            $inject = [
                'url' => route('gudang.acces.update', $id),
                'divisi' => Divisi::pluck('name', 'id')->toArray(),
                'acces' => $acces
            ];
        }
        return view('gudang.accessories.barcode', $inject);
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
        $acces = Accessories::findOrFail($id);

        // Hapus semua item di tabel item_ins yang memiliki no_seri yang sama
        AccessoriesIn::where('accessories_id', $acces->id)->delete();

        // Hapus item dari tabel items
        $acces->delete();

        // Tampilkan alert sukses

        return back()->withSuccess('Success', 'Delete accessories Success');
    }

    private function save(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|',
        ],[
            'name.required' => 'Nama Accessories Wajib Diisi',
        ]);

        if ($id === null) {
            // Dapatkan dua digit terakhir tahun, bulan, dan hari
            $currentDate = date('md'); // contoh: 240822 untuk 22 Agustus 2024

            // Dapatkan jumlah accessories yang diinput pada hari ini
            $countToday = Accessories::whereDate('created_at', date('Y-m-d'))->count();

            // Nomor urut dengan padding tiga digit
            $newCode = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // Format kode akses baru
            $codeAcces = 'P-' . $currentDate . $newCode; // contoh output: P-240822001
        }

        $acces = Accessories::firstOrNew(['id' => $id]);
        $acces->name          = $request->input('name');
        $acces->price         = 0;
        $acces->divisi_id     = Auth::user()->divisi_id;
        $acces->capital_price = 0;
        if ($id === null) {
            $acces->code_acces = $codeAcces;
        }
        $acces->save();

        Alert::success('Success', 'Save Data Success');
        return redirect()->route('gudang.acces.index');
    }


    public function editmultiple()
    {
        $accessories = Accessories::all(); // Ambil semua accessories, atau sesuaikan dengan kebutuhan Anda
        return view('gudang.accessories.edit', compact('accessories'));
    }
    public function checkCode(Request $request)
    {
        $code = $request->get('code_access');
        $accessory = Accessories::where('divisi_id', Auth::user()->divisi_id)->where('code_acces', $code)->first();

        if ($accessory) {
            return response()->json(['exists' => true, 'data' => $accessory]);
        } else {
            return response()->json(['exists' => false, 'message' => 'Code Accessories or No Seri not found!']);
        }
    }

    public function updateMultiple(Request $request)
    {
        $request->validate([
            'accessories' => 'required|array',
            'accessories.*.code_acces' => 'required|string',
            'accessories.*.stok' => 'required|integer|min:0',
            'accessories.*.kode_msk' => 'nullable|string',
        ]);

        $accessoriesData = $request->input('accessories');

        foreach ($accessoriesData as $accessoryData) {
            $accessory = Accessories::where('code_acces', $accessoryData['code_acces'])->first();

            if ($accessory) {
                // Perbarui stok
                $accessory->stok += $accessoryData['stok'];
                $accessory->save();

                // Update atau buat entri di AccessoriesIn
                $today = now()->format('Y-m-d');
                $existingAccessoriesIn = AccessoriesIn::where('accessories_id', $accessory->id)
                    ->whereDate('date_in', $today)
                    ->first();

                if ($existingAccessoriesIn) {
                    $existingAccessoriesIn->qty += $accessoryData['stok'];
                    $existingAccessoriesIn->save();
                } else {
                    AccessoriesIn::create([
                        'accessories_id' => $accessory->id,
                        'qty' => $accessoryData['stok'],
                        'kode_msk' => $accessoryData['kode_msk'] ?? null,
                        'date_in' => now(),
                    ]);
                }

                // Cek apakah kode invoice sudah ada
                $invoice = $accessoryData['kode_msk'];
                $existingPembelian = Pembelian::where('invoice', $invoice)->first();

                // Jika invoice belum ada, buat entri baru di tabel `pembelian`
                if (!$existingPembelian && $invoice) {
                    Pembelian::create([
                        'divisi_id' => Auth::user()->divisi_id,
                        'invoice' => $invoice,
                        'status' => '1', // status bisa disesuaikan jika perlu
                    ]);
                }

            } else {
                return response()->json(['success' => false, 'message' => 'Accessory not found for code: ' . $accessoryData['code_acces']], 404);
            }
        }

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
        $report = AccessoriesSale::whereHas('accessories')->with('sale', 'accessories')->get();
        return view('gudang.report-acces.index', compact('report'));
    }
    public function accesin()
    {
        $accesin = AccessoriesIn::whereHas('accessories.divisi', function ($query) {
            $query->where('divisi_id', Auth::user()->divisi_id);
        })
            ->with(['accessories' => function ($query) {
                $query->select('id', 'price', 'name', 'code_acces'); // Pilih kolom yang relevan
            }])
            ->get()
            ->map(function ($item) {
                // Tambahkan total_price berdasarkan price dan qty
                $item->total_price = $item->accessories->price * $item->qty;
                return $item;
            });

        return view('gudang.accessories.accesin', compact('accesin'));
    }


    public function accesout(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $accesout = AccessoriesSale::whereHas('sale.divisi', function ($query) {
            $query->where('divisi_id', Auth::user()->divisi_id);
        })
            ->whereHas('accessories') // pastikan relasi accessories tidak null
            ->with(['accessories', 'accessories.accessoriesIn', 'sale'])
            ->get()
            ->map(function ($accessorySale) {
                $accessorySale->total_price = $accessorySale->accessories->price * $accessorySale->qty;
                return $accessorySale;
            });

        if ($bulan) {
            $accesout = $accesout->filter(function ($acces) use ($bulan) {
                return \Carbon\Carbon::parse($acces->acces_out)->format('m') == $bulan;
            });
        }

        if ($tahun) {
            $accesout = $accesout->filter(function ($acces) use ($tahun) {
                return \Carbon\Carbon::parse($acces->acces_out)->format('Y') == $tahun;
            });
        }

        $result = $accesout->groupBy('accessories_id')->map(function ($group) {
            $stok_awal = $group->first()->accessories->accessoriesIn->sum('qty');
            $total_keluar = $group->sum('qty');
            $stok_sisa = $stok_awal - $total_keluar;

            return [
                'stok_awal' => $stok_awal,
                'total_keluar' => $total_keluar,
                'stok_sisa' => $stok_sisa,
                'data' => $group
            ];
        });

        if ($request->ajax()) {
            return response()->json($result);
        }

        return view('gudang.accessories.accesin', compact('bulan', 'tahun'));
    }

    public function print(Request $request)
    {
        // Ambil ID accessories yang dipilih dan jumlah barcode
        $selectedAccessories = $request->input('accessories', []);
        $barcodeQuantities = $request->input('barcode_quantity', []);

        // Validasi apakah ada accessories yang dipilih
        if (!$request->has('accessories') || !is_array($request->accessories)) {
            return redirect()->back()->withErrors(['error' => 'Pilih minimal 4 accessories untuk dicetak.']);
        }

        // Ambil data accessories yang dipilih
        $accessories = Accessories::whereIn('id', $request->accessories)->get();

        if ($accessories->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'Accessories yang dipilih tidak ditemukan.']);
        }

        // Hitung total jumlah barcode yang diminta
        $totalBarcodes = 0;
        foreach ($request->input('barcode_quantity', []) as $quantity) {
            $totalBarcodes += (int)$quantity;
        }

        // Validasi jumlah minimal barcode atau accessories
        if ($totalBarcodes < 4 && count($request->accessories) < 4) {
            return redirect()->back()->withErrors(['error' => 'Jika jumlah barcode kurang dari 4, maka minimal pilih 4 accessories.']);
        }


        // Ambil data accessories berdasarkan ID yang dipilih
        $barcodes = Accessories::whereIn('id', $selectedAccessories)
            ->select('id', 'code_acces', 'name')
            ->get();

        $generator = new BarcodeGeneratorHTML();
        $barcodePaths = [];
        $invalidBarcodes = [];

        // Generate barcode untuk masing-masing accessories
        foreach ($barcodes as $accessory) {
            $codeLength = strlen($accessory->code_acces);

            // Validasi panjang barcode
            if ($codeLength > 9 || $codeLength < 1) {
                $invalidBarcodes[] = $accessory->name . ' (' . $accessory->code_acces . ')';
                continue;
            }

            $quantity = $barcodeQuantities[$accessory->id] ?? 1; // Default 1 jika tidak diisi
            $barcodePaths[$accessory->id] = [];
            for ($i = 0; $i < $quantity; $i++) {
                $barcodePaths[$accessory->id][] = $generator->getBarcode($accessory->code_acces, $generator::TYPE_CODE_128,2 , 25, 'black', false);            }
        }

        // Jika ada barcode tidak valid, tampilkan pesan error
        if (!empty($invalidBarcodes)) {
            $errorMessages = 'Kode berikut tidak valid (panjang harus 1-9 karakter): ' . implode(', ', $invalidBarcodes);
            return back()->with('error', $errorMessages);
        }

        // Render PDF
        $html = view('gudang.accessories.barcode-pdf', [
            'accessories' => $barcodes,
            'barcodePath' => $barcodePaths,
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('barcode.pdf', ['Attachment' => false]);
    }
    public function listReject()
    {
        $reject = AccessoriesRejecte::all();
        return view('gudang.accesreject.index', compact('reject'));
    }public function kembali()
    {
        return view('gudang.accesreject.create');
    }
    public function reject(Request $request, $id = null)
    {
        // Mengecek apakah 'code_acces' di-input-kan secara manual
        if (empty($request->input('code_acces'))) {
            // Jika kosong, buat kode akses baru secara otomatis
            $currentDate = date('md'); // contoh: 240822 untuk 22 Agustus 2024

            // Dapatkan jumlah accessories yang diinput pada hari ini
            $countToday = AccessoriesRejecte::whereDate('created_at', date('Y-m-d'))->count();

            // Nomor urut dengan padding tiga digit
            $newCode = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // Format kode akses baru
            $codeAcces = 'P-' . $newCode . $currentDate; // contoh output: P-240822001
        } else {
            // Jika kode akses di-inputkan secara manual
            $codeAcces = $request->input('code_acces');
        }

        // Buat objek AccessoriesRejecte baru
        $acces = new AccessoriesRejecte();
        $acces->divisi_id = Auth::user()->divisi_id;
        $acces->name = $request->input('name');
        $acces->price = $request->input('price');
        $acces->capital_price = $request->input('capital_price');
        $acces->code_acces = $codeAcces;  // Jika tidak ada ID, akan otomatis menggunakan kode yang dibuat
        $acces->stok = $request->input('stok');

        // Simpan data ke database
        $acces->save();

        // Mengecek apakah kode akses yang sama sudah ada di tabel Accessories
        $existingAccessory = Accessories::where('code_acces', $codeAcces)->first();

        if ($existingAccessory) {
            // Jika ditemukan, kurangi stok accessories yang sesuai dengan jumlah yang diinputkan
            $existingAccessory->stok -= $request->input('stok');

            // Pastikan stok tidak menjadi negatif
            if ($existingAccessory->stok < 0) {
                $existingAccessory->stok = 0; // Jika stok kurang dari 0, set ke 0
            }

            // Simpan perubahan stok ke dalam database
            $existingAccessory->save();
        }

        return back()->with('success', 'Accessories Reject Ditambah dan Stok Accessories Dikurangi');
    }
    public function deleteReject($id)
    {
        AccessoriesRejecte::whereId($id)->delete();
        return back()->with('success', 'Accessories Reject Dihapus');
    }
    public function updatein(Request $request, $id)
    {
        $item = AccessoriesIn::findOrFail($id);
        $item->update([
            'kode_msk' => $request->kode_msk,
            'date_in' => $request->date_in,
        ]);

        // Cek apakah invoice sudah ada di tabel `pembelian`
        $invoice = $request->kode_msk;
        $existingPembelian = Pembelian::where('invoice', $invoice)->first();

        // Jika invoice belum ada, buat entri baru di tabel `pembelian`
        if (!$existingPembelian && $invoice) {
            Pembelian::create([
                'divisi_id' => Auth::user()->divisi_id,
                'invoice' => $invoice,
                'status' => '1',
            ]);
        }

        return back()->with('success', 'Accessories Berhasil Diedit');
    }


}
