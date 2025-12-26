<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store; // BARU: Import Model Store
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Tampilkan daftar semua pegawai.
     */
    public function index()
    {
        // Ambil user beserta info store-nya (Eager Loading agar ringan)
        // Kita juga urutkan: Admin di atas, baru Dapur
        $users = User::with('store')
                    ->orderBy('role', 'asc')
                    ->latest()
                    ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Tampilkan form tambah pegawai baru.
     */
    public function create()
    {
        // BARU: Kirim data gerai ke view agar bisa dipilih di dropdown
        $stores = Store::all();

        return view('admin.users.create', compact('stores'));
    }

    /**
     * Simpan data pegawai baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:admin,dapur'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // LOGIKA BARU: store_id WAJIB DIISI jika role-nya adalah 'dapur'
            'store_id' => ['required_if:role,dapur', 'nullable', 'exists:stores,id'],
        ], [
            // Custom Error Message agar lebih jelas
            'store_id.required_if' => 'Jika posisi adalah Dapur, Anda wajib memilih Gerai.',
        ]);

        // 2. Simpan ke Database
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),

            // Simpan ID Gerai (Hanya jika role dapur, jika admin null)
            'store_id' => $request->role === 'dapur' ? $request->store_id : null,
        ]);

        // 3. Redirect
        return redirect()->route('admin.users.index')
            ->with('success', 'Pegawai baru berhasil ditambahkan!');
    }

    /**
     * Hapus pegawai.
     */
    public function destroy(User $user)
    {
        // Mencegah hapus diri sendiri
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pegawai berhasil dihapus.');
    }
}
