<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\Goal;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'user' => Auth::user(),
        ]);
    }

    public function data(): JsonResponse
    {
        $userId = Auth::id();

        $activities = Aktivitas::where('user_id', $userId)
            ->orderBy('deadline')
            ->get()
            ->map(fn (Aktivitas $act) => [
                'id'         => $act->id,
                'judul'      => $act->judul,
                'deskripsi'  => $act->deskripsi,
                'kategoriId' => $act->kategori_id,
                'deadline'   => $act->deadline ? Carbon::parse($act->deadline)->toISOString() : null,
                'prioritas'  => $act->prioritas ?? 'Sedang',
                'status'     => $act->status ?? 'Belum Dimulai',
            ])
            ->values();

        $categories = Kategori::where('user_id', $userId)
            ->get()
            ->map(fn (Kategori $cat) => [
                'id'    => $cat->id,
                'name'  => $cat->name,
                'color' => $cat->color ?? '#64748b',
            ])
            ->values();

        $goals = Goal::where('user_id', $userId)
            ->orderBy('id')
            ->get()
            ->map(fn (Goal $goal) => [
                'id'      => $goal->id,
                'judul'   => $goal->judul,
                'progres' => (int) ($goal->progres ?? 0),
            ])
            ->values();

        return response()->json([
            'activities' => $activities,
            'categories' => $categories,
            'goals'      => $goals,
        ]);
    }
}