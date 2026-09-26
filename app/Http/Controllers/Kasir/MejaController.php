<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Meja;

class MejaController extends Controller
{
    public function index(\App\Services\Kasir\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Status Meja';
        
        $mejas = Meja::all();
        
        return view('kasir.meja.index', compact('data', 'mejas'));
    }
}
