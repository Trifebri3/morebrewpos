<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\DashboardService;

class KeuanganController extends Controller
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

    public function pengeluaran()
    {
        return view('admin.placeholder', ['data' => $this->getViewData('Data Pengeluaran'), 'title' => 'Data Pengeluaran']);
    }

    public function shift()
    {
        return view('admin.placeholder', ['data' => $this->getViewData('Jadwal Shift'), 'title' => 'Jadwal Shift']);
    }

    public function absensi()
    {
        return view('admin.placeholder', ['data' => $this->getViewData('Data Absensi'), 'title' => 'Data Absensi']);
    }
}
