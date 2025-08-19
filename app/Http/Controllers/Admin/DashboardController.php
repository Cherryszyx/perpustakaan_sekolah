<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kunjungan;
use Carbon\Carbon;

class DashboardController extends Controller
{
public function index()
{
    $kunjungan = Kunjungan::with('user')
        ->where('via_qr', true)
        ->orderBy('waktu_kunjungan', 'desc')
        ->get();

    $kunjunganHariIni = Kunjungan::where('via_qr', true)
        ->whereDate('waktu_kunjungan', Carbon::today())
        ->count();

    return view('admin.dashboard', compact('kunjungan', 'kunjunganHariIni'));
}
}