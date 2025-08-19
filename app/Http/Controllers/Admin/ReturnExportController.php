<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restore;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReturnExportController extends Controller
{
    public function exportPdf(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $restores = Restore::with(['user', 'book'])
            ->whereYear('returned_at', $year)
            ->whereMonth('returned_at', $month)
            ->whereIn('status', ['Past due', 'Fine not paid'])
            ->get();

        $totalDenda = $restores->sum('fine');

        $pdf = Pdf::loadView('admin.returns-pdf', compact('restores', 'month', 'year', 'totalDenda'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("laporan-denda-bulan-$month-$year.pdf");
    }
}