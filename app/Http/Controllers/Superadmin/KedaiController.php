<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kedai;
use App\Services\Superadmin\DashboardService;

class KedaiController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    private function getViewData()
    {
        return $this->dashboardService->getDashboardData();
    }

    public function index()
    {
        $data = $this->getViewData();
        $kedais = Kedai::all();
        return view('superadmin.kedai.index', compact('data', 'kedais'));
    }

    public function create()
    {
        $data = $this->getViewData();
        return view('superadmin.kedai.create', compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        Kedai::create($validated);
        return redirect()->route('superadmin.kedai.index')->with('success', 'Kedai created successfully.');
    }

    public function edit(Kedai $kedai)
    {
        $data = $this->getViewData();
        return view('superadmin.kedai.edit', compact('data', 'kedai'));
    }

    public function update(Request $request, Kedai $kedai)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'wifi_ssid' => 'nullable|string|max:255',
            'wifi_password' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $kedai->update([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'wifi_ssid' => $validated['wifi_ssid'] ?? null,
            'wifi_password' => $validated['wifi_password'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('superadmin.kedai.index')->with('success', 'Kedai updated successfully.');
    }

    public function destroy(Kedai $kedai)
    {
        $kedai->delete();
        return redirect()->route('superadmin.kedai.index')->with('success', 'Kedai deleted successfully.');
    }

    public function performa()
    {
        $data = $this->getViewData();
        $kedais = Kedai::all();
        // Mock performa data
        $performaData = [];
        foreach($kedais as $k) {
            $performaData[] = [
                'kedai' => $k,
                'omzet' => rand(1000000, 50000000),
                'transactions' => rand(50, 500),
                'rating' => rand(35, 50) / 10,
            ];
        }
        return view('superadmin.kedai.performa', compact('data', 'performaData'));
    }
}
