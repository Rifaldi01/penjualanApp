<?php

namespace App\Http\Controllers\Gudang;

use App\Models\Divisi;
use App\Models\Pembelian;
use App\Models\Permintaan;
use App\Models\Accessories;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AccessoriesIn;
use App\Models\DetailAccessories;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Permintaan::where('status', 'pending')
            ->whereDate('created_at', '<', now()->subDays(7))
            ->delete();
        // Jika user adalah pemohon (yang meminta barang)
        $permintaans = Permintaan::with(['detailAccessories', 'divisiAsal', 'divisiTujuan'])
            ->where('divisi_id_tujuan', $request->user()->divisi_id)
            ->orderBy('status', 'asc') // Mengurutkan dari data yang baru ditambahkan
            ->get();

        return view('gudang.permintaan.index', compact('permintaans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $divisi = Divisi::where('id', '!=', Auth::user()->divisi_id)->get();
        return view('gudang.permintaan.create', compact('divisi'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'divisi_id_asal' => 'required|exists:divisis,id',
            'accessories_id.*' => 'required|exists:accessories,id',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        $divisiAsal = $validated['divisi_id_asal'];
        $totalJumlah = 0; // Variabel untuk menghitung total jumlah

        foreach ($validated['accessories_id'] as $index => $accessories_id) {
            $jumlahDiminta = $validated['jumlah'][$index];

            // Ambil stok dari divisi asal
            $accessory = Accessories::where('id', $accessories_id)
                ->where('divisi_id', $divisiAsal)
                ->first();

            if (!$accessory) {
                return redirect()->back()->withErrors("Accessories dengan ID $accessories_id tidak ditemukan di divisi {$divisiAsal}.");
            }

            if ($accessory->stok < $jumlahDiminta) {
                return redirect()->back()->withErrors("Stok untuk {$accessory->name} di divisi {$divisiAsal} hanya tersedia {$accessory->stok}.");
            }

            // Tambahkan jumlah diminta ke total jumlah
            $totalJumlah += $jumlahDiminta;
        }

        // Buat permintaan setelah semua validasi selesai
        $permintaan = Permintaan::create([
            'divisi_id_asal' => $divisiAsal,
            'divisi_id_tujuan' => $request->user()->divisi_id,
            'kode' => 'REQ-' . strtoupper(uniqid()),
            'jumlah' => $totalJumlah, // Simpan total jumlah di sini
            'status' => 'pending',
        ]);

        foreach ($validated['accessories_id'] as $index => $accessories_id) {
            $jumlahDiminta = $validated['jumlah'][$index];

            // Buat entri detail aksesori
            DetailAccessories::create([
                'permintaan_id' => $permintaan->id,
                'accessories_id' => $accessories_id,
                'qty' => (string) $jumlahDiminta,
            ]);
        }

        return redirect()->route('gudang.permintaan.index')->with('success', 'Permintaan berhasil dibuat!');
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
    public function edit(Permintaan $permintaan)
    {
        $aksesoris = Accessories::all();
        $divisi = Divisi::all();
        return view('gudang.permintaan.edit', compact('permintaan', 'aksesoris', 'divisi'));
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
        $permintaan = Permintaan::findOrFail($id);

        // Validasi input
        $request->validate([
            'status' => 'required|in:disetujui,tidak_disetujui',
        ]);

        // Update status permintaan
        $permintaan->status = $request->status;
        $permintaan->save();

        // Jika status adalah 'disetujui', kurangi stok dari divisi tujuan
        if ($request->status === 'disetujui') {
            $accessory = Accessories::find($permintaan->accessories_id);

            if ($accessory) {
                if ($accessory->stok >= $permintaan->jumlah) {
                    // Kurangi stok
                    $accessory->stok -= $permintaan->jumlah;
                    $accessory->save();
                } else {
                    return redirect()->back()->withErrors([
                        'stok' => 'Stok barang tidak mencukupi untuk permintaan ini.',
                    ]);
                }
            }
        }

        return redirect()
            ->route('gudang.permintaan.index')
            ->with('success', 'Permintaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permintaan $permintaan)
    {
        $permintaan->delete();
        return redirect()->route('gudang.permintaan.index')->with('success', 'Permintaan berhasil dihapus.');
    }

    public function approve($id)
    {
        $permintaan = Permintaan::with(['detailAccessories', 'divisiAsal', 'divisiTujuan'])->findOrFail($id);

        if (Auth::user()->divisi_id != $permintaan->divisi_id_tujuan) {
            return redirect()->back()->withErrors('Anda tidak berhak menyetujui permintaan ini.');
        }

        $kode_msk = 'PMT-' . str_pad($permintaan->id, 5, '0', STR_PAD_LEFT);
        $total_item = 0;
        $total_harga = 0;

        foreach ($permintaan->detailAccessories as $detail) {
            $accessoriesAsal = Accessories::where('id', $detail->accessories_id)
                ->where('divisi_id', $permintaan->divisi_id_asal)
                ->first();

            if (!$accessoriesAsal || $accessoriesAsal->stok < $detail->qty) {
                return redirect()->back()->withErrors('Stok divisi asal tidak mencukupi untuk aksesori ' . ($accessoriesAsal->name ?? ''));
            }

            $accessoriesAsal->decrement('stok', $detail->qty);

            $accessoriesTujuan = Accessories::where('code_acces', $accessoriesAsal->code_acces)
                ->where('divisi_id', $permintaan->divisi_id_tujuan)
                ->first();

            if ($accessoriesTujuan) {
                $accessoriesTujuan->increment('stok', $detail->qty);
            } else {
                $accessoriesTujuan = Accessories::create([
                    'divisi_id'     => $permintaan->divisi_id_tujuan,
                    'name'          => $accessoriesAsal->name,
                    'price'         => $accessoriesAsal->price,
                    'capital_price' => $accessoriesAsal->capital_price,
                    'code_acces'    => $accessoriesAsal->code_acces,
                    'stok'          => $detail->qty,
                ]);
            }

            AccessoriesIn::create([
                'accessories_id' => $accessoriesTujuan->id,
                'price'          => $accessoriesTujuan->price,
                'capital_price'  => $accessoriesTujuan->capital_price,
                'ppn'            => 0,
                'qty'            => $detail->qty,
                'kode_msk'       => $kode_msk,
                'date_in'        => now(),
            ]);

            $total_item  += $detail->qty;
            $total_harga += $accessoriesTujuan->price * $detail->qty;
        }

        $permintaan->update(['status' => 'diterima']);

        return redirect()->route('gudang.permintaan.index')->with('success', 'Permintaan berhasil diterima.');
    }




    public function receive($id)
    {
        $permintaan = Permintaan::findOrFail($id);

        // Pastikan user hanya dapat menerima jika mereka yang membuat permintaan
        if (Auth::user()->divisi_id != $permintaan->divisi_id_asal) {
            return redirect()->back()->withErrors('Anda tidak berhak menerima permintaan ini.');
        }

        $permintaan->update(['status' => 'disetujui']);

        return redirect()->route('gudang.permintaan.konfirmasi')->with('success', 'Permintaan disetujui.');
    }
    public function konfirmasi(Request $request)
    {
        // Hapus permintaan dengan status "pending" jika sudah lebih dari 7 hari sejak dibuat
        Permintaan::where('status', 'pending')
            ->whereDate('created_at', '<', now()->subDays(7))
            ->delete();

        // Ambil daftar permintaan setelah penghapusan
        $permintaans = Permintaan::with(['detailAccessories.accessories', 'divisiAsal', 'divisiTujuan'])
            ->where('divisi_id_asal', $request->user()->divisi_id)
            ->orderBy('status', 'asc')
            ->get();

        return view('gudang.permintaan.konfirmasi', compact('permintaans'));
    }

    public function fetchAccessories($divisi_id)
    {
        $accessories = Accessories::where('divisi_id', $divisi_id)
            ->where('stok', '>', 0)
            ->get(['id', 'code_acces', 'name', 'price', 'stok']);

        return response()->json($accessories);
    }

}
