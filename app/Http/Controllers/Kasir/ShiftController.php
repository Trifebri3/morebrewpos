<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(\App\Services\Kasir\DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        $data['title'] = 'Jadwal Shift';

        // Retrieve schedules, similar to Admin
        $hari_urutan = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4,
            'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7
        ];

        $shifts = \App\Models\Shift::with('users')->get()->sortBy(function($shift) use ($hari_urutan) {
            return $hari_urutan[$shift->hari] ?? 99;
        });

        // Group by day for view
        $groupedShifts = $shifts->groupBy('hari');

        return view('kasir.shift.index', array_merge($data, [
            'groupedShifts' => $groupedShifts,
            'hari_urutan' => $hari_urutan
        ]));
    }
}
