<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AktivitasController extends Controller
{
    public function data()
    {
        $aktivitas = Aktivitas::with('kategori')
            ->where('user_id', Auth::id())
            ->orderBy('deadline', 'asc')
            ->get();

        return response()->json($aktivitas);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'deadline' => 'required|date',
            'prioritas' => 'nullable|in:Rendah,Sedang,Tinggi',
            'status' => 'nullable|in:Belum Dimulai,Sedang Dikerjakan,Selesai',
            'deskripsi' => 'nullable|string',
        ]);

        $aktivitas = Aktivitas::create([
            'user_id' => Auth::id(),
            'kategori_id' => $validated['kategori_id'],
            'judul' => $validated['judul'],
            'deadline' => $validated['deadline'],
            'prioritas' => $validated['prioritas'] ?? $this->hitungPrioritas($validated['deadline']),
            'status' => $validated['status'] ?? 'Belum Dimulai',
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return response()->json(['message' => 'Aktivitas berhasil ditambahkan', 'data' => $aktivitas], 201);
    }

    public function update(Request $request, $id)
    {
        $aktivitas = Aktivitas::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'deadline' => 'required|date',
            'prioritas' => 'nullable|in:Rendah,Sedang,Tinggi',
            'status' => 'required|in:Belum Dimulai,Sedang Dikerjakan,Selesai',
            'deskripsi' => 'nullable|string',
        ]);

        $aktivitas->update([
            'judul' => $validated['judul'],
            'kategori_id' => $validated['kategori_id'],
            'deadline' => $validated['deadline'],
            'prioritas' => $validated['prioritas'] ?? $this->hitungPrioritas($validated['deadline']),
            'status' => $validated['status'],
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return response()->json(['message' => 'Aktivitas berhasil diperbarui', 'data' => $aktivitas]);
    }

    public function markDone($id)
    {
        $aktivitas = Aktivitas::where('user_id', Auth::id())->findOrFail($id);
        $aktivitas->update(['status' => 'Selesai']);

        return response()->json(['message' => 'Aktivitas ditandai selesai']);
    }

    public function destroy($id)
    {
        $aktivitas = Aktivitas::where('user_id', Auth::id())->findOrFail($id);
        $aktivitas->delete();

        return response()->json(['message' => 'Aktivitas berhasil dihapus']);
    }

    private function hitungPrioritas($deadline)
    {
        $jamTersisa = Carbon::now()->diffInHours(Carbon::parse($deadline), false);
        if ($jamTersisa <= 24) return 'Tinggi';
        if ($jamTersisa <= 72) return 'Sedang';
        return 'Rendah';
    }
}