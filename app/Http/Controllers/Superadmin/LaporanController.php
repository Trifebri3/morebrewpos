<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Superadmin\DashboardService;

class LaporanController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    private function getViewData($title)
    {
        $data = $this->dashboardService->getDashboardData();
        $data['title'] = $title;
        return $data;
    }

    public function transaksi()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Seluruh Transaksi'), 'title' => 'Seluruh Transaksi']);
    }

    public function omzet()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Omzet Keseluruhan'), 'title' => 'Omzet Keseluruhan']);
    }

    public function lengkap()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Laporan Lengkap'), 'title' => 'Laporan Lengkap']);
    }
}
