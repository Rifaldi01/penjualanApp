<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $report = Sale::with('customer', 'accessories', 'itemSales')->get();
        $report->each(function($sale) {
            $sale->accessories_list = $sale->accessories->pluck('name')->implode(', ');

            $sale->itemSales = $sale->itemSales->map(function($itemSale) {
                return $itemSale->name;
            })->implode(', ');
        });
        //return $report;
        $income = $report->sum('pay');
        return view('admin.report.index', compact('report', 'income'));
    }
    public function filter(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sale::query();

        // Apply date filter if both dates are provided
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $report = $query->with('customer', 'accessories', 'itemSales') // Assuming you have a relationship with the customer model
        ->get();

        $report->each(function($sale) {
            $sale->accessories_list = $sale->accessories->pluck('name')->implode(', ');

            $sale->itemSales = $sale->itemSales->map(function($itemSale) {
                return $itemSale->name . ' - ' .'('. $itemSale->no_seri . ')';
            });
        });

        $income = $report->sum('pay'); // Assuming 'pay' is the column representing total income

        return response()->json([
            'report' => $report,
            'income' => $income
        ]);
    }
}
