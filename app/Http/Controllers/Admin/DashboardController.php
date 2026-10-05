<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();

        return view('admin.dashboard', compact('data'));
    }

    public function realtimeData(): JsonResponse
    {
        return response()->json($this->dashboardService->getRealtimeMetrics());
    }
}
