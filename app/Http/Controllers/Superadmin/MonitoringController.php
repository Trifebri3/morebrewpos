<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Superadmin\DashboardService;

class MonitoringController extends Controller
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

    public function stok()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Monitoring Stok'), 'title' => 'Monitoring Stok']);
    }

    public function kas()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Monitoring Kas'), 'title' => 'Monitoring Kas']);
    }

    public function absensi()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Monitoring Absensi'), 'title' => 'Monitoring Absensi']);
    }

    public function auditLog()
    {
        return view('superadmin.placeholder', ['data' => $this->getViewData('Audit Log'), 'title' => 'Audit Log']);
    }
}
