<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Tampilkan daftar meja dengan Pagination.
     */
    public function index()
    {
        // PERBAIKAN DI SINI: Ubah paginate(10) menjadi paginate(5)
        // Ini akan membatasi tampilan hanya 5 meja per halaman agar desain konsisten & tidak panjang.
        $tables = Table::orderBy('name', 'asc')->paginate(5);

        return view('admin.tables.index', compact('tables'));
    }

    /**
     * Simpan data meja baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:tables,name',
        ], [
            'name.required' => 'Nama/Kode meja wajib diisi.',
            'name.unique' => 'Kode meja ini sudah terdaftar, gunakan nama lain.',
            'name.max' => 'Nama meja terlalu panjang.'
        ]);

        Table::create([
            'name' => strtoupper($request->name),
            'status' => 'available',
        ]);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja baru berhasil ditambahkan!');
    }

    /**
     * Tampilkan halaman edit (Form Edit + List Tabel).
     */
    public function edit(Table $table)
    {
        // PERBAIKAN DI SINI JUGA: Ubah menjadi paginate(5)
        // Agar saat mode edit, tabel di sampingnya tetap rapih (hanya 5 baris).
        $tables = Table::orderBy('name', 'asc')->paginate(5);

        return view('admin.tables.index', compact('tables', 'table'));
    }

    /**
     * Update data meja.
     */
    public function update(Request $request, Table $table)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:tables,name,' . $table->id,
            'status' => 'required|in:available,occupied',
        ], [
            'name.required' => 'Nama meja tidak boleh kosong.',
            'name.unique' => 'Nama meja sudah digunakan oleh meja lain.'
        ]);

        $table->update([
            'name' => strtoupper($request->name),
            'status' => $request->status,
        ]);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Data meja berhasil diperbarui!');
    }

    /**
     * Hapus meja.
     */
    public function destroy(Table $table)
    {
        if ($table->status === 'occupied') {
            return back()->withErrors(['error' => 'Meja sedang TERISI dan tidak bisa dihapus. Kosongkan status terlebih dahulu.']);
        }

        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil dihapus dari sistem.');
    }
}
