<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\ReturSale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ReturController extends Controller
{
    public function index()
    {
        $returSales = ReturSale::with(['sale','user','divisi'])
            ->latest()
            ->get();

        return view('manager.sale.retur', compact('returSales'));
    }
    public function show($id)
    {
        // Ambil data retur + relasi yang dibutuhkan
        $retur = ReturSale::with([
            'sale' => function ($q) {
                $q->withTrashed()
                    ->with([
                        'itemSales' => fn ($q) => $q->withTrashed(),
                        'accessoriesSales' => fn ($q) => $q->withTrashed(),
                    ]);
            },
            'user',
            'divisi',
        ])->findOrFail($id);

        // Otorisasi: admin boleh lihat semua, user hanya divisinya

        return view('manager.sale.detail-retur', compact('retur'));
    }

    public function print($id)
    {
        $retur = $this->getReturData($id);

        return view('manager.sale.retur-print', compact('retur'));
    }

    /**
     * EXPORT PDF
     */
    public function exportPdf($id)
    {
        $retur = $this->getReturData($id);

        $pdf = Pdf::loadView('manager.sale.retur-print', compact('retur'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('RETUR-' . $retur->divisi->name . '.pdf');
    }

    /**
     * EXPORT EXCEL (TANPA Export Class)
     */
    public function exportExcel()
    {
        $returs = ReturSale::with([
            'sale' => function ($q) {
                $q->withTrashed()
                    ->with([
                        'itemSales' => fn ($q) => $q->withTrashed(),
                        'accessoriesSales' => fn ($q) => $q->withTrashed(),
                    ]);
            },
            'user',
            'divisi'
        ])->latest()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Invoice Retur');
        $sheet->setCellValue('B1', 'Invoice Sale');
        $sheet->setCellValue('C1', 'Divisi');
        $sheet->setCellValue('D1', 'User');
        $sheet->setCellValue('E1', 'Tipe');
        $sheet->setCellValue('F1', 'Nama');
        $sheet->setCellValue('G1', 'Kode / No Seri');
        $sheet->setCellValue('H1', 'Qty');
        $sheet->setCellValue('I1', 'Harga');
        $sheet->setCellValue('J1', 'Subtotal');

        $row = 2;

        foreach ($returs as $retur) {

            // ITEM
            foreach ($retur->sale?->itemSales ?? [] as $item) {
                $sheet->fromArray([
                    $retur->invoice_retur,
                    $retur->sale?->invoice,
                    $retur->divisi?->name,
                    $retur->user?->name,
                    'Item',
                    $item->name,
                    $item->no_seri,
                    1,
                    $item->price,
                    $item->price,
                ], null, 'A' . $row++);
            }

            // ACCESSORIES
            foreach ($retur->sale?->accessoriesSales ?? [] as $acc) {
                $sheet->fromArray([
                    $retur->invoice_retur,
                    $retur->sale?->invoice,
                    $retur->divisi?->name,
                    $retur->user?->name,
                    'Accessories',
                    $acc->accessories?->name ?? 'Deleted',
                    $acc->accessories?->code_acces ?? '-',
                    $acc->qty,
                    $acc->subtotal / max($acc->qty, 1),
                    $acc->subtotal,
                ], null, 'A' . $row++);
            }
        }

        $writer = new Xlsx($spreadsheet);

        $fileName = 'retur-sales-' . date('Y-m-d') . '.xlsx';
        $tempPath = storage_path('app/' . $fileName);

        $writer->save($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    /**
     * AMBIL DATA RETUR + OTORISASI (REUSABLE)
     */
    private function getReturData($id)
    {
        $retur = ReturSale::with([
            'sale' => function ($q) {
                $q->withTrashed()
                    ->with([
                        'itemSales' => fn ($q) => $q->withTrashed(),
                        'accessoriesSales' => fn ($q) => $q->withTrashed(),
                    ]);
            },
            'user',
            'divisi',
        ])->findOrFail($id);

        // Otorisasi: admin atau divisi terkait

        return $retur;
    }

}
