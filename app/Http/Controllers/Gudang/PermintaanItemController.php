<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\DetailItem;
use App\Models\Divisi;
use App\Models\Item;
use App\Models\ItemIn;
use App\Models\PermintaanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermintaanItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        PermintaanItem::where('status', 'pending')
            ->whereDate('created_at', '<', now()->subDays(7))
            ->delete();
        $permintaans = PermintaanItem::with(['detailItem', 'divisiAsal', 'divisiTujuan'])
            ->where('divisi_id_tujuan', $request->user()->divisi_id)
            ->orderBy('status', 'asc') // Mengurutkan dari data yang baru ditambahkan
            ->get();

        return view('gudang.permintaanitem.index', compact('permintaans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $divisi = Divisi::where('id', '!=', Auth::user()->divisi_id)->get();
        return view('gudang.permintaanitem.create', compact( 'divisi'));
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
            'item_in_id.*' => 'required|exists:item_ins,id', // Memastikan ID Item yang diminta valid
            'jumlah.*' => 'required|integer|min:1', // Validasi jumlah yang diminta
        ]);

        $divisiAsal = $validated['divisi_id_asal'];
        $totalJumlah = 0; // Variabel untuk menghitung total jumlah

        // Menghitung total jumlah berdasarkan jumlah per item
        foreach ($validated['jumlah'] as $jumlahItem) {
            $totalJumlah += $jumlahItem; // Menambahkan jumlah item ke total
        }

        // Membuat permintaan item dan menyimpan total jumlah
        $permintaan = PermintaanItem::create([
            'divisi_id_asal' => $divisiAsal,
            'divisi_id_tujuan' => $request->user()->divisi_id,
            'kode' => 'REQ-' . strtoupper(uniqid()),
            'jumlah' => $totalJumlah, // Mengisi kolom jumlah dengan total jumlah yang diminta
            'status' => 'pending',
        ]);

        // Menyimpan detail item tanpa kolom jumlah di tabel detail_items
        foreach ($validated['item_in_id'] as $index => $item_in_id) {
            DetailItem::create([
                'permintaan_item_id' => $permintaan->id,
                'item_in_id' => $item_in_id, // Menyimpan ID item yang diminta
            ]);
        }

        // Redirect kembali dengan pesan sukses
        return redirect()->route('gudang.permintaanitem.index')->with('success', 'Permintaan berhasil dibuat!');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function fetchAccessories($divisi_id)
    {
        $accessories = ItemIn::where('divisi_id', $divisi_id)->get();
        return response()->json($accessories);
    }
    public function konfirmasi(Request $request)
    {
        PermintaanItem::where('status', 'pending')
            ->whereDate('created_at', '<', now()->subDays(7))
            ->delete();
        $permintaans = PermintaanItem::with(['detailItem.itemIn', 'divisiAsal', 'divisiTujuan'])
            ->where('divisi_id_asal', $request->user()->divisi_id)
            ->orderBy('status', 'asc')
            ->get();
        return view('gudang.permintaanitem.konfirmasi', compact('permintaans', ));
    }
    public function approve($id)
    {
        // Ambil permintaan item beserta relasi terkait
        $permintaan = PermintaanItem::with(['detailItem', 'divisiAsal', 'divisiTujuan'])->findOrFail($id);

        // Validasi: Pastikan hanya divisi tujuan yang dapat menyetujui
        if (Auth::user()->divisi_id != $permintaan->divisi_id_tujuan) {
            return redirect()->back()->withErrors('Anda tidak berhak menyetujui permintaan ini.');
        }

        // Proses setiap detail item
        foreach ($permintaan->detailItem as $detail) {
            // Ambil item dari divisi asal
            $itemAsal = ItemIn::where('id', $detail->item_in_id)
                ->where('divisi_id', $permintaan->divisi_id_asal)
                ->first();

            // Pastikan item ditemukan sebelum melakukan update
            if ($itemAsal) {
                // Update divisi_id pada tabel item_in ke divisi tujuan (yang sedang login)
                $itemInUpdate = ItemIn::where('id', $detail->item_in_id)
                    ->update(['divisi_id' => Auth::user()->divisi_id]);

                // Ambil data item berdasarkan no_seri dari tabel items
                $item = Item::where('no_seri', $itemAsal->no_seri)->first();

                // Pastikan item ditemukan dan update divisi_id pada tabel items
                if ($item) {
                    $item->divisi_id = Auth::user()->divisi_id; // Update divisi_id ke divisi tujuan
                    $item->save(); // Simpan perubahan
                }
            }
        }

        // Update status permintaan menjadi 'diterima'
        $permintaan->update(['status' => 'diterima']);

        // Redirect ke halaman permintaan dengan pesan sukses
        return redirect()->route('gudang.permintaan.index')->with('success', 'Permintaan berhasil diterima.');
    }



    public function receive($id)
    {
        $permintaan = PermintaanItem::findOrFail($id);

        // Pastikan user hanya dapat menerima jika mereka yang membuat permintaan
        if (Auth::user()->divisi_id != $permintaan->divisi_id_asal) {
            return redirect()->back()->withErrors('Anda tidak berhak menerima permintaan ini.');
        }

        $permintaan->update(['status' => 'disetujui']);

        return redirect()->route('gudang.permintaanitem.konfirmasi')->with('success', 'Permintaan disetujui.');
    }
}
