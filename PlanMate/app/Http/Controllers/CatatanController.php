<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatatanController extends Controller
{
    public function index()
    {
        return view('catatan');
    }

    public function getData()
    {
        $data = DB::table('catatans')->orderByDesc('id')->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah' => 'required|string|max:100',
            'judul'       => 'required|string|max:150',
            'tanggal'     => 'nullable|date',
            'isi'         => 'required|string',
        ]);

        $id = DB::table('catatans')->insertGetId([
            'mata_kuliah' => $validated['mata_kuliah'],
            'judul'       => $validated['judul'],
            'tanggal'     => $validated['tanggal'],
            'isi'         => $validated['isi'],
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mata_kuliah' => 'required|string|max:100',
            'judul'       => 'required|string|max:150',
            'tanggal'     => 'nullable|date',
            'isi'         => 'required|string',
        ]);

        DB::table('catatans')->where('id', $id)->update([
            'mata_kuliah' => $validated['mata_kuliah'],
            'judul'       => $validated['judul'],
            'tanggal'     => $validated['tanggal'],
            'isi'         => $validated['isi'],
            'updated_at'  => now(),
        ]);

        return response()->json(['message' => 'Diupdate']);
    }

    public function destroy($id)
    {
        DB::table('catatans')->where('id', $id)->delete();
        return response()->json(['message' => 'Dihapus']);
    }
}