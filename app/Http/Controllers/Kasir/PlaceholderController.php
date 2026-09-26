<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Kasir\DashboardService;

class PlaceholderController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function show(Request $request)
    {
        $data = $this->dashboardService->getDashboardData();
        // Coba ambil nama route, lalu ubah jadi title yang bagus
        $routeName = $request->route()->getName(); // e.g. kasir.absensi
        $parts = explode('.', $routeName);
        $title = ucwords(str_replace('_', ' ', end($parts)));
        
        $data['title'] = $title;
        return view('kasir.placeholder', ['data' => $data, 'title' => $title]);
    }
}
