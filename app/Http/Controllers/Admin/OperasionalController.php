<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\DashboardService;

class OperasionalController extends Controller
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

    public function meja()
    {
        return view('admin.placeholder', ['data' => $this->getViewData('Manajemen Meja & QR'), 'title' => 'Manajemen Meja & QR']);
    }

    public function laporan()
    {
        return view('admin.placeholder', ['data' => $this->getViewData('Laporan Transaksi'), 'title' => 'Laporan Transaksi']);
    }

    public function pengaturan()
    {
        return view('admin.placeholder', ['data' => $this->getViewData('Pengaturan Kedai'), 'title' => 'Pengaturan Kedai']);
    }
}
