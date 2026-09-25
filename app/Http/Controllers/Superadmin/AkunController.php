<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kedai;
use App\Services\Superadmin\DashboardService;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
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
        // Hanya tampilkan admin dan kasir
        $users = User::whereIn('role', ['admin', 'kasir'])->with('kedai')->get();
        return view('superadmin.akun.index', compact('data', 'users'));
    }

    public function create()
    {
        $data = $this->getViewData();
        $kedais = Kedai::where('is_active', true)->get();
        return view('superadmin.akun.create', compact('data', 'kedais'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,kasir',
            'kedai_id' => 'required|exists:kedais,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);
        return redirect()->route('superadmin.akun.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $data = $this->getViewData();
        $kedais = Kedai::where('is_active', true)->get();
        return view('superadmin.akun.edit', compact('data', 'user', 'kedais'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,kasir',
            'kedai_id' => 'required|exists:kedais,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        return redirect()->route('superadmin.akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('superadmin.akun.index')->with('success', 'Akun berhasil dihapus.');
    }

    public function roles()
    {
        $data = $this->getViewData();
        return view('superadmin.akun.roles', compact('data'));
    }

    public function impersonate(User $user)
    {
        if (auth()->id() == $user->id) {
            return back()->with('error', 'Tidak bisa impersonate diri sendiri.');
        }
        
        session(['impersonator_id' => auth()->id()]);
        auth()->login($user);
        
        if ($user->role == 'admin') return redirect()->route('admin.dashboard');
        if ($user->role == 'kasir') return redirect()->route('kasir.dashboard');
        
        return redirect('/dashboard');
    }
}
