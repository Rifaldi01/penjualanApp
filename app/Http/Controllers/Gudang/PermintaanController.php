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
use Illuminate\Support\Facades\DB;


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
            ->whereNotIn('status', ['retur pending', 'retur'])
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
    public function destroy($id)
    {
        $permintaan = Permintaan::findOrFail($id);

        // Hapus semua item di tabel item_ins yang memiliki no_seri yang sama
        DetailAccessories::where('permintaan_id', $permintaan->id)->delete();

        // Hapus item dari tabel items
        $permintaan->delete();
        return redirect()->route('gudang.permintaan.index')->with('success', 'Permintaan berhasil dibatalkan.');
    }

    public function approve($id)
    {
        $permintaan = Permintaan::with(['detailAccessories', 'divisiAsal', 'divisiTujuan'])
            ->findOrFail($id);

        if (Auth::user()->divisi_id != $permintaan->divisi_id_tujuan) {
            return redirect()->back()->withErrors('Anda tidak berhak menyetujui permintaan ini.');
        }

        // 1. VALIDASI STOK TERLEBIH DAHULU
        foreach ($permintaan->detailAccessories as $detail) {
            $accessoriesAsal = Accessories::where('id', $detail->accessories_id)
                ->where('divisi_id', $permintaan->divisi_id_asal)
                ->first();

            if (!$accessoriesAsal || $accessoriesAsal->stok < $detail->qty) {
                return redirect()->back()->withErrors(
                    'Stok tidak mencukupi untuk accessories: ' . ($accessoriesAsal->name ?? '-')
                );
            }
        }

        // 2. JALANKAN PROSES DALAM TRANSACTION
        DB::transaction(function () use ($permintaan) {

            $kode_msk = 'PMT-' . str_pad($permintaan->id, 5, '0', STR_PAD_LEFT);

            foreach ($permintaan->detailAccessories as $detail) {

                $accessoriesAsal = Accessories::where('id', $detail->accessories_id)
                    ->where('divisi_id', $permintaan->divisi_id_asal)
                    ->first();

                // Kurangi stok divisi asal
                $accessoriesAsal->decrement('stok', $detail->qty);

                // Cari / buat accessories di divisi tujuan
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

                // Catat accessories masuk
                AccessoriesIn::create([
                    'accessories_id' => $accessoriesTujuan->id,
                    'price'          => $accessoriesTujuan->price,
                    'capital_price'  => $accessoriesTujuan->capital_price,
                    'ppn'            => 0,
                    'qty'            => $detail->qty,
                    'kode_msk'       => $kode_msk,
                    'date_in'        => now(),
                ]);
            }

            // Update status permintaan
            $permintaan->update(['status' => 'diterima']);
        });

        return redirect()
            ->route('gudang.permintaan.index')
            ->with('success', 'Permintaan berhasil diterima.');
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
            ->whereNotIn('status', ['retur pending', 'retur'])
            ->where('divisi_id_asal', $request->user()->divisi_id)
            ->orderBy('status', 'asc')
            ->get();

        return view('gudang.permintaan.konfirmasi', compact('permintaans'));
    }

    public function fetchAccessories($divisi_id)
    {
        $accessories = Accessories::where('divisi_id', $divisi_id)
            ->where('stok', '>', 0)
            ->get(['id', 'created_at', 'code_acces', 'name', 'price', 'stok']);

        return response()->json($accessories);
    }
    public function retur(Request $request)
    {
       // Ambil daftar permintaan setelah penghapusan
        $returs = Permintaan::with(['detailAccessories.accessories', 'divisiAsal', 'divisiTujuan'])
            ->whereIn('status', ['retur pending', 'retur'])
            ->where('divisi_id_asal', $request->user()->divisi_id)
            ->orderBy('status', 'asc')
            ->get();
        $returminta = Permintaan::with(['detailAccessories.accessories', 'divisiAsal', 'divisiTujuan'])
            ->whereIn('status', ['retur pending', 'retur'])
            ->where('divisi_id_tujuan', $request->user()->divisi_id)
            ->orderBy('status', 'asc')
            ->get();

        return view('gudang.permintaan.retur', compact('returs','returminta'));
    }
    public function returRequest($id)
    {
        $permintaan = Permintaan::findOrFail($id);

        if (Auth::user()->divisi_id != $permintaan->divisi_id_tujuan) {
            return back()->withErrors('Tidak berhak retur.');
        }

        if ($permintaan->status != 'diterima') {
            return back()->withErrors('Status belum diterima.');
        }

        $permintaan->update([
            'status' => 'retur pending'
        ]);

        return back()->with('success','Permintaan retur dikirim.');
    }
    public function returApprove($id)
    {
        $permintaan = Permintaan::with('detailAccessories.accessories')->findOrFail($id);

        if (Auth::user()->divisi_id != $permintaan->divisi_id_asal) {
            return back()->withErrors('Tidak berhak menerima retur.');
        }

        if ($permintaan->status != 'retur pending') {
            return back()->withErrors('Status tidak valid.');
        }

        DB::transaction(function () use ($permintaan) {

            $kode_msk = 'PMT-' . str_pad($permintaan->id, 5, '0', STR_PAD_LEFT);

            foreach ($permintaan->detailAccessories as $detail) {

                if (!$detail->accessories) {
                    throw new \Exception('Accessories tidak ditemukan.');
                }

                // Barang di divisi tujuan
                $barangTujuan = Accessories::where('code_acces', $detail->accessories->code_acces)
                    ->where('divisi_id', $permintaan->divisi_id_tujuan)
                    ->first();

                if (!$barangTujuan) {
                    throw new \Exception('Barang tujuan tidak ditemukan.');
                }

                if ($barangTujuan->stok < $detail->qty) {
                    throw new \Exception('Stok retur tidak cukup.');
                }

                // Kurangi stok tujuan
                $barangTujuan->decrement('stok', $detail->qty);

                // Tambah stok asal
                $barangAsal = Accessories::where('code_acces', $detail->accessories->code_acces)
                    ->where('divisi_id', $permintaan->divisi_id_asal)
                    ->first();

                if ($barangAsal) {
                    $barangAsal->increment('stok', $detail->qty);
                } else {
                    Accessories::create([
                        'divisi_id'     => $permintaan->divisi_id_asal,
                        'name'          => $detail->accessories->name,
                        'price'         => $detail->accessories->price,
                        'capital_price' => $detail->accessories->capital_price,
                        'code_acces'    => $detail->accessories->code_acces,
                        'stok'          => $detail->qty,
                    ]);
                }

                /*
                HAPUS DATA ACCESSORIES_IN
                yang dibuat saat approve
                */
                AccessoriesIn::where('accessories_id', $barangTujuan->id)
                    ->where('kode_msk', $kode_msk)
                    ->where('qty', $detail->qty)
                    ->delete();
            }

            $permintaan->update([
                'status' => 'retur'
            ]);
        });

        return back()->with('success', 'Retur diterima.');
    }
}
