<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $report = Sale::with('customer')->get();
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

        $report = $query->with('customer') // Assuming you have a relationship with the customer model
        ->get();

        $income = $report->sum('pay'); // Assuming 'pay' is the column representing total income

        return response()->json([
            'report' => $report,
            'income' => $income
        ]);
    }
}
