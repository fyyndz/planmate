<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    // READ: Mengambil daftar kategori milik user beserta jumlah tugas
    public function data()
    {
        $kategoris = Kategori::where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($kategori) {
                $kategori->aktivitas_count = DB::table('aktivitas')
                    ->where('kategori_id', $kategori->id)
                    ->count();
                return $kategori;
            });

        return response()->json($kategoris);
    }

    // CREATE: Menyimpan kategori baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        $kategori = Kategori::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'color' => $validated['color'] ?? '#4f46e5',
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json(['message' => 'Kategori berhasil ditambahkan', 'data' => $kategori], 201);
    }

    // UPDATE: Memperbarui kategori
    public function update(Request $request, $id)
    {
        $kategori = Kategori::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        $kategori->update($validated);

        return response()->json(['message' => 'Kategori berhasil diperbarui', 'data' => $kategori]);
    }

    // DELETE: Menghapus kategori
    public function destroy($id)
    {
        $kategori = Kategori::where('user_id', Auth::id())->findOrFail($id);
        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}