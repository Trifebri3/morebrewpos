<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Superadmin\DashboardService;

class PengaturanController extends Controller
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

    public function produk()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Produk Global'), 'title' => 'Produk Global']);
    }

    public function printer()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Pengaturan Printer'), 'title' => 'Pengaturan Printer']);
    }

    public function pajak()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Pengaturan Pajak & Service'), 'title' => 'Pengaturan Pajak & Service']);
    }

    public function qr()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Sistem QR'), 'title' => 'Sistem QR']);
    }
}
