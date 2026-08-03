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
        // 1. Quick Stats
        $totalLowongan = Lowongan::count();
        $totalPelamar = Pelamar::count();
        $totalDiterima = Lamaran::where('status', 'diterima')->count();

        // 2. Ringkasan Cepat
        $activeLowongan = Lowongan::all()->filter(fn($l) => $l->status === 'aktif')->count();
        $totalLamaran = Lamaran::count();
        $acceptanceRate = $totalLamaran > 0 ? round(($totalDiterima / $totalLamaran) * 100, 1) : 0;

        // 3. Status Distribution (Donut Chart)
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

        // 4. Monthly Chart Data
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
        $maxChartValue = 1; // Prevent division by zero
        
        // Find max value up to current month (or all 12 if you prefer, but usually showing up to current month or all 12 is fine)
        // We will show 8 months starting from a few months back, or just all 12. Let's do 12.
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

        // Set minimal batas atas 10 agar perubahan visual batang grafik terlihat jelas
        // saat jumlah data pelamar masih tergolong sedikit.
        $maxChartValue = max(10, $maxChartValue);

        return view('admin.dashboard', compact(
            'totalLowongan', 'totalPelamar', 'totalDiterima',
            'activeLowongan', 'totalLamaran', 'acceptanceRate',
            'statusData', 'chartData', 'maxChartValue', 'currentYear'
        ));
    }
}
