<?php

namespace App\Http\Controllers;

use App\Models\Lampiran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LampiranController extends Controller
{
    /** Read: Ambil semua lampiran milik user yang sedang login (bisa difilter per aktivitas_id). */
    public function data(Request $request): JsonResponse
    {
        $query = Lampiran::where('user_id', Auth::id())->latest();

        if ($request->filled('aktivitas_id')) {
            $query->where('aktivitas_id', (int) $request->aktivitas_id);
        }

        $items = $query->get()->map(fn (Lampiran $item) => $item->toFrontend());

        return response()->json($items);
    }

    /** Create: Unggah lampiran baru dan simpan ke storage + MySQL. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'aktivitas_id'   => ['nullable', 'integer'],
            'judul_lampiran' => ['required', 'string', 'max:255'],
            'keterangan'     => ['nullable', 'string'],
            'file'           => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar,txt', 'max:10240'],
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store('lampiran/' . Auth::id(), 'public');

        $lampiran = Lampiran::create([
            'user_id'        => Auth::id(),
            'aktivitas_id'   => $data['aktivitas_id'] ?? null,
            'judul_lampiran' => $data['judul_lampiran'],
            'nama_file_asli' => $uploaded->getClientOriginalName(),
            'path_file'      => $path,
            'ekstensi'       => $uploaded->getClientOriginalExtension(),
            'ukuran_file'    => $uploaded->getSize(),
            'keterangan'     => $data['keterangan'] ?? null,
        ]);

        return response()->json($lampiran->toFrontend(), 201);
    }

    public function download(int $id): StreamedResponse
    {
        $lampiran = $this->ownLampiran($id);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if (! $disk->exists($lampiran->path_file)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan.');
        }

        return $disk->download($lampiran->path_file, $lampiran->nama_file_asli);
    }

    /** Update: Ubah metadata atau ganti berkas lampiran dengan file baru. */
    public function update(Request $request, int $id): JsonResponse
    {
        $lampiran = $this->ownLampiran($id);

        $data = $request->validate([
            'aktivitas_id'   => ['nullable', 'integer'],
            'judul_lampiran' => ['required', 'string', 'max:255'],
            'keterangan'     => ['nullable', 'string'],
            'file'           => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar,txt', 'max:10240'],
        ]);

        $updatePayload = [
            'aktivitas_id'   => $data['aktivitas_id'] ?? $lampiran->aktivitas_id,
            'judul_lampiran' => $data['judul_lampiran'],
            'keterangan'     => $data['keterangan'] ?? null,
        ];

        // Jika pengguna mengunggah file pengganti, hapus file lama dan simpan yang baru
        if ($request->hasFile('file')) {
            if ($lampiran->path_file && Storage::disk('public')->exists($lampiran->path_file)) {
                Storage::disk('public')->delete($lampiran->path_file);
            }

            $uploaded = $request->file('file');
            $updatePayload['path_file']      = $uploaded->store('lampiran/' . Auth::id(), 'public');
            $updatePayload['nama_file_asli'] = $uploaded->getClientOriginalName();
            $updatePayload['ekstensi']       = $uploaded->getClientOriginalExtension();
            $updatePayload['ukuran_file']    = $uploaded->getSize();
        }

        $lampiran->update($updatePayload);

        return response()->json($lampiran->fresh()->toFrontend());
    }

    /** Delete: Hapus berkas fisik dari storage dan hapus record dari database. */
    public function destroy(int $id): JsonResponse
    {
        $lampiran = $this->ownLampiran($id);

        if ($lampiran->path_file && Storage::disk('public')->exists($lampiran->path_file)) {
            Storage::disk('public')->delete($lampiran->path_file);
        }

        $lampiran->delete();

        return response()->json(null, 204);
    }

    private function ownLampiran(int $id): Lampiran
    {
        return Lampiran::where('user_id', Auth::id())->findOrFail($id);
    }
}