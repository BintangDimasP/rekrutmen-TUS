<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Lamaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // menampilkan jumlah data
        $totalLowongan = Lowongan::count();
        $totalPelamar = Pelamar::count();
        $totalDiterima = Lamaran::where('status', 'diterima')->count();

        // data statistik lowongan : pelamar diterima
        $activeLowongan = Lowongan::all()->filter(fn($l) => $l->status === 'aktif')->count();
        $totalLamaran = Lamaran::count();
        $acceptanceRate = $totalLamaran > 0 ? round(($totalDiterima / $totalLamaran) * 100, 1) : 0;

        // data pie chart status
        $statusCounts = Lamaran::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        $countMenunggu = $statusCounts['menunggu'] ?? 0;
        $countProses = ($statusCounts['seleksi_tahap1'] ?? 0) + ($statusCounts['seleksi_tahap2'] ?? 0);
        $countDiterima = $statusCounts['diterima'] ?? 0;
        $countDitolak = $statusCounts['ditolak'] ?? 0;
        $countMengundurkan = $statusCounts['mengundurkan_diri'] ?? 0;
        
        $statusData = [
            'total' => $totalLamaran,
            'menunggu' => $countMenunggu,
            'proses' => $countProses,
            'diterima' => $countDiterima,
            'ditolak' => $countDitolak,
            'mengundurkan' => $countMengundurkan,
        ];

        // data bar chart/grafik
        $currentYear = Carbon::now()->year;
        
        $monthExpr = 'MONTH(created_at)';

        $monthlyLamaran = Lamaran::select(
                DB::raw("$monthExpr as month"),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyDiterima = Lamaran::select(
                DB::raw("$monthExpr as month"),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', $currentYear)
            ->where('status', 'diterima')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $chartData = [];
        $maxChartValue = 1;
        
        for ($i = 1; $i <= 12; $i++) {
            $lamaranCount = $monthlyLamaran[$i] ?? 0;
            $diterimaCount = $monthlyDiterima[$i] ?? 0;
            
            if ($lamaranCount > $maxChartValue) {
                $maxChartValue = $lamaranCount;
            }
            
            $chartData[] = [
                'month' => $months[$i - 1],
                'lamaran' => $lamaranCount,
                'diterima' => $diterimaCount,
            ];
        }

        $maxChartValue = max(10, $maxChartValue);

        return view('admin.dashboard', compact(
            'totalLowongan', 'totalPelamar', 'totalDiterima',
            'activeLowongan', 'totalLamaran', 'acceptanceRate',
            'statusData', 'chartData', 'maxChartValue', 'currentYear'
        ));
    }
}
