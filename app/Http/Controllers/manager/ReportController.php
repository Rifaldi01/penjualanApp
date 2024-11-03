<?php

namespace App\Http\Controllers\manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $report = Sale::with('customer')->get();
        $income = $report->sum('pay');
        return view('manager.report.index', compact('report', 'income'));
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

        $totalIncome = 0;
        $totalCapital = 0;

        $report->each(function($sale) use (&$totalIncome, &$totalCapital) {
            // Sum up total sales income
            $totalIncome += $sale->pay;

            // Calculate capital price for each accessory in the sale
            $accessoryCapital = $sale->accessories->sum(function($accessory) {
                return $accessory->pivot->qty * $accessory->capital_price;
            });

            // Calculate capital price for each item in the sale
            $itemCapital = $sale->itemSales->sum(function($itemSale) {
                return $itemSale->capital_price;
            });

            // Accumulate total capital cost for the sale
            $totalCapital += $accessoryCapital + $itemCapital;

            // Format the accessories list for each sale
            $sale->accessories_list = $sale->accessories->pluck('name')->implode(', ');

            // Format item sales list with name and serial number
            $sale->itemSales = $sale->itemSales->map(function($itemSale) {
                return $itemSale->name . ' - (' . $itemSale->no_seri . ')';
            });
        });

        // Calculate profit
        $profit = $totalIncome - $totalCapital;

        return response()->json([
            'report' => $report,
            'income' => $totalIncome,
            'profit' => $profit
        ]);
    }
}
