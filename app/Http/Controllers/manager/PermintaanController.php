<?php

namespace App\Http\Controllers\Manager;

use App\Models\Divisi;
use App\Models\Permintaan;
use App\Models\Accessories;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        // Jika user adalah pemohon (yang meminta barang)
        $permintaans = Permintaan::with(['accessories', 'divisiAsal', 'divisiTujuan'])
            ->where('divisi_id_tujuan', $request->user()->divisi_id)
            ->orderBy('status', 'asc') // Mengurutkan dari data yang baru ditambahkan
            ->get();

        return view('manager.permintaan.index', compact('permintaans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $divisi = Divisi::where('id', '!=', Auth::user()->divisi_id)->get();
        return view('manager.permintaan.create', compact( 'divisi'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'divisi_id_asal' => 'required|exists:divisis,id',
            'accessories_id.*' => 'required|exists:accessories,id',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        $divisiAsal = $validated['divisi_id_asal'];

        foreach ($validated['accessories_id'] as $index => $accessories_id) {
            $jumlahDiminta = $validated['jumlah'][$index];

            // Ambil stok dari divisi asal
            $accessory = Accessories::where('id', $accessories_id)
                ->where('divisi_id', $divisiAsal)
                ->first();

            if (!$accessory) {
                return redirect()->back()->withErrors("Accessories dengan ID $accessories_id tidak ditemukan di divisi {$accessory->divisi->name}.");
            }

            if ($accessory->stok < $jumlahDiminta) {
                return redirect()->back()->withErrors("Stok untuk {$accessory->name} di divisi {$accessory->divisi->name} hanya tersedia {$accessory->stok}.");
            }

            $now   = Carbon::now();
            $bulan = $now->format('m');
            $tahun = $now->format('y');

            $lastPermintaan = Permintaan::whereMonth('created_at', $bulan)
                ->whereYear('created_at', $now->year)
                ->orderBy('id', 'desc')
                ->first();

            $lastNumber = 0;

            if ($lastPermintaan) {
                // Ambil nomor dari kode sebelumnya
                $lastNumber = (int) explode('/', $lastPermintaan->kode)[2];
            }

            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            $kodePermintaan = "PMT/ACS/{$nextNumber}/{$bulan}/{$tahun}";

            // Buat permintaan jika stok mencukupi
            Permintaan::create([
                'accessories_id' => $accessories_id,
                'divisi_id_asal' => $divisiAsal,
                'divisi_id_tujuan' => $request->user()->divisi_id,
                'kode' => $kodePermintaan,
                'jumlah' => (string) $jumlahDiminta, // Simpan jumlah sebagai string
                'status' => 'pending',
            ]);
        }

        return redirect()->route('manager.permintaan.index')->with('success', 'Permintaan berhasil dibuat!');
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
        return view('manager.permintaan.edit', compact('permintaan', 'aksesoris', 'divisi'));
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
            ->route('manager.permintaan.index')
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
        return redirect()->route('manager.permintaan.index')->with('success', 'Permintaan berhasil dihapus.');
    }


    public function approve($id)
    {
        $permintaan = Permintaan::findOrFail($id);

        // Pastikan user hanya dapat menyetujui jika mereka yang diminta
        if (Auth::user()->divisi_id != $permintaan->divisi_id_tujuan) {
            return redirect()->back()->withErrors('Anda tidak berhak menyetujui permintaan ini.');
        }

        // Ambil aksesori dari divisi asal
        $accessoriesAsal = Accessories::where('id', $permintaan->accessories_id)
            ->where('divisi_id', $permintaan->divisi_id_asal)
            ->first();

        if (!$accessoriesAsal || $accessoriesAsal->stok < $permintaan->jumlah) {
            return redirect()->back()->withErrors('Stok divisi asal tidak mencukupi.');
        }

        // Kurangi stok di divisi asal
        $accessoriesAsal->update(['stok' => $accessoriesAsal->stok - $permintaan->jumlah]);

        // Tambahkan stok di divisi tujuan
        $accessoriesTujuan = Accessories::where('name', $accessoriesAsal->name)
            ->where('divisi_id', $permintaan->divisi_id_tujuan)
            ->first();

        if ($accessoriesTujuan) {
            // Jika aksesori sudah ada di divisi tujuan, tambahkan stok
            $accessoriesTujuan->update(['stok' => $accessoriesTujuan->stok + $permintaan->jumlah]);
        } else {
            // Jika belum ada, buat aksesori baru di divisi tujuan
            Accessories::create([
                'divisi_id' => $permintaan->divisi_id_tujuan,
                'name' => $accessoriesAsal->name,
                'price' => $accessoriesAsal->price,
                'capital_price' => $accessoriesAsal->capital_price,
                'code_acces' => 'P-' . rand(1000000, 9999999), // Format kode akses unik
                'stok' => $permintaan->jumlah,
            ]);
        }

        // Update status permintaan
        $permintaan->update(['status' => 'diterima']);

        return redirect()->route('manager.permintaan.index')->with('success', 'Permintaan Diterima.');
    }
    public function receive($id)
    {
        $permintaan = Permintaan::findOrFail($id);

        // Pastikan user hanya dapat menerima jika mereka yang membuat permintaan
        if (Auth::user()->divisi_id != $permintaan->divisi_id_asal) {
            return redirect()->back()->withErrors('Anda tidak berhak menerima permintaan ini.');
        }

        $permintaan->update(['status' => 'disetujui']);

        return redirect()->route('manager.permintaan.konfirmasi')->with('success', 'Permintaan disetujui.');
    }
    public function konfirmasi(Request $request)
    {
        $permintaans = Permintaan::with(['accessories', 'divisiAsal', 'divisiTujuan'])
            ->where('divisi_id_asal', $request->user()->divisi_id)
            ->orderBy('status', 'asc') // Mengurutkan dari data yang baru ditambahkan
            ->get();
        return view('manager.permintaan.konfirmasi', compact('permintaans', ));
    }
    public function fetchAccessories($divisi_id)
    {
        $accessories = Accessories::where('divisi_id', $divisi_id)
                    ->where('stok', '>', 0)
                    ->get(['id', 'name', 'price', 'stok']);
        return response()->json($accessories);
    }
}
