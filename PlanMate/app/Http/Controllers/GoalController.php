<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    /** Daftar semua goals milik user yang login (dipakai untuk menyegarkan halaman). */
    public function data(): JsonResponse
    {
        return response()->json(Goal::forFrontend(Auth::id()));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $goal = DB::transaction(function () use ($data) {
            $goal = Goal::create([
                'user_id'      => Auth::id(),
                'aktivitas_id' => $data['aktivitasId'] ?? null,
                'judul'        => $data['judul'],
                'deskripsi'    => $data['deskripsi'] ?? null,
                'deadline'     => $data['deadline'] ?? null,
                'progres'      => 0,
            ]);

            $this->syncTahapan($goal, $data['targetKecil'] ?? []);
            $this->applyProgress($goal, (int) ($data['progres'] ?? 0));

            return $goal;
        });

        return response()->json($goal->fresh('tahapan')->toFrontend(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $goal = $this->ownGoal($id);
        $data = $this->validated($request);

        DB::transaction(function () use ($goal, $data) {
            $goal->update([
                'aktivitas_id' => $data['aktivitasId'] ?? null,
                'judul'        => $data['judul'],
                'deskripsi'    => $data['deskripsi'] ?? null,
                'deadline'     => $data['deadline'] ?? null,
            ]);

            $this->syncTahapan($goal, $data['targetKecil'] ?? []);
            $this->applyProgress($goal, (int) ($data['progres'] ?? 0));
        });

        return response()->json($goal->fresh('tahapan')->toFrontend());
    }

    public function destroy(int $id): JsonResponse
    {
        $this->ownGoal($id)->delete(); // tahapan ikut terhapus (cascade)

        return response()->json(null, 204);
    }

    /** Ubah progres lewat slider "Update Cepat". */
    public function progress(Request $request, int $id): JsonResponse
    {
        $goal = $this->ownGoal($id);
        $p = (int) $request->validate([
            'progres' => ['required', 'integer', 'between:0,100'],
        ])['progres'];

        $goal->update(['progres' => $p]);

        // Kalau punya tahapan: 100% = semua selesai, 0% = semua belum
        if (in_array($p, [0, 100], true) && $goal->tahapan()->exists()) {
            $goal->tahapan()->update(['done' => $p === 100]);
        }

        return response()->json($goal->fresh('tahapan')->toFrontend());
    }

    /** Centang / batal centang satu tahapan, progres dihitung ulang. */
    public function toggleTahapan(int $id, int $tahapanId): JsonResponse
    {
        $goal = $this->ownGoal($id);
        $tahapan = $goal->tahapan()->findOrFail($tahapanId);
        $tahapan->update(['done' => ! $tahapan->done]);

        $this->applyProgress($goal, (int) $goal->progres);

        return response()->json($goal->fresh('tahapan')->toFrontend());
    }

    // ------------------------------------------------------------------

    /** Hanya boleh mengakses goal milik sendiri (selain itu 404). */
    private function ownGoal(int $id): Goal
    {
        return Goal::with('tahapan')->where('user_id', Auth::id())->findOrFail($id);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'judul'               => ['required', 'string', 'max:255'],
            'deskripsi'           => ['nullable', 'string'],
            'deadline'            => ['nullable', 'date'],
            'aktivitasId'         => ['nullable', 'integer'],
            'progres'             => ['nullable', 'integer', 'between:0,100'],
            'targetKecil'         => ['nullable', 'array'],
            'targetKecil.*.text'  => ['required', 'string', 'max:255'],
            'targetKecil.*.done'  => ['nullable', 'boolean'],
        ]);
    }

    /** Ganti seluruh tahapan goal dengan daftar yang dikirim. */
    private function syncTahapan(Goal $goal, array $rows): void
    {
        $goal->tahapan()->delete();

        foreach ($rows as $row) {
            $goal->tahapan()->create([
                'text' => $row['text'],
                'done' => (bool) ($row['done'] ?? false),
            ]);
        }
    }

    /**
     * Kalau ada tahapan: progres = persen tahapan selesai.
     * Kalau tidak ada: pakai nilai manual.
     */
    private function applyProgress(Goal $goal, int $manual): void
    {
        $goal->load('tahapan');
        $total = $goal->tahapan->count();

        $progres = $total > 0
            ? (int) round($goal->tahapan->where('done', true)->count() / $total * 100)
            : $manual;

        $goal->update(['progres' => max(0, min(100, $progres))]);
    }
}
