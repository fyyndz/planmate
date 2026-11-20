
<?php

use App\Http\Controllers\AktivitasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LampiranController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Halaman untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Halaman yang wajib login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ---- Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    // ---- Halaman Lainnya
    Route::view('/profil', 'profil');
    Route::view('/pencarian', 'pencarian');
    Route::view('/kalender', 'kalender');
    Route::view('/deadline', 'deadline');

    // ---- Lampiran
    Route::view('/lampiran', 'lampiran');
    Route::get('/lampiran/data', [LampiranController::class, 'data']);
    Route::post('/lampiran', [LampiranController::class, 'store']);
    Route::get('/lampiran/{id}/download', [LampiranController::class, 'download'])->whereNumber('id');
    Route::put('/lampiran/{id}', [LampiranController::class, 'update'])->whereNumber('id');
    Route::delete('/lampiran/{id}', [LampiranController::class, 'destroy'])->whereNumber('id');

    // ---- Kategori
    Route::view('/kategori', 'kategori');
    Route::get('/kategori/data', [KategoriController::class, 'data']);
    Route::post('/kategori', [KategoriController::class, 'store']);
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->whereNumber('id');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->whereNumber('id');

    // ---- Aktivitas
    Route::view('/aktivitas', 'aktivitas');
    Route::get('/aktivitas/data', [AktivitasController::class, 'data']);
    Route::post('/aktivitas', [AktivitasController::class, 'store']);
    Route::put('/aktivitas/{id}', [AktivitasController::class, 'update'])->whereNumber('id');
    Route::patch('/aktivitas/{id}/done', [AktivitasController::class, 'markDone'])->whereNumber('id');
    Route::delete('/aktivitas/{id}', [AktivitasController::class, 'destroy'])->whereNumber('id');

    // ---- Catatan Materi Kuliah
    Route::view('/catatan', 'catatan');

    Route::get('/catatan/data', function () {
        return DB::table('catatans')
            ->where('user_id', auth()->id())
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn ($c) => [
                'id'      => $c->id,
                'subject' => $c->mata_kuliah,
                'title'   => $c->judul,
                'date'    => $c->tanggal,
                'content' => $c->isi,
            ])->values();
    });

    $rules = [
        'subject' => 'required|string|max:100',
        'title'   => 'required|string|max:150',
        'date'    => 'nullable|date',
        'content' => 'required|string',
    ];

    Route::post('/catatan', function (Request $r) use ($rules) {
        $v = $r->validate($rules);

        $id = DB::table('catatans')->insertGetId([
            'user_id'     => auth()->id(),
            'mata_kuliah' => $v['subject'],
            'judul'       => $v['title'],
            'tanggal'     => $v['date'] ?? null,
            'isi'         => $v['content'],
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['id' => $id], 201);
    });

    Route::put('/catatan/{id}', function (Request $r, $id) use ($rules) {
        $v = $r->validate($rules);

        DB::table('catatans')
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->update([
                'mata_kuliah' => $v['subject'],
                'judul'       => $v['title'],
                'tanggal'     => $v['date'] ?? null,
                'isi'         => $v['content'],
                'updated_at'  => now(),
            ]);

        return response()->json(['ok' => true]);
    })->whereNumber('id');

    Route::delete('/catatan/{id}', function ($id) {
        DB::table('catatans')
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->delete();

        return response()->json(['ok' => true]);
    })->whereNumber('id');

    // ---- Goals
    Route::view('/goals', 'goals');
    Route::get('/goals/data', [GoalController::class, 'data']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::put('/goals/{id}', [GoalController::class, 'update'])->whereNumber('id');
    Route::delete('/goals/{id}', [GoalController::class, 'destroy'])->whereNumber('id');
    Route::patch('/goals/{id}/progress', [GoalController::class, 'progress'])->whereNumber('id');
    Route::patch('/goals/{id}/tahapan/{tahapanId}/toggle', [GoalController::class, 'toggleTahapan'])
        ->whereNumber('id')->whereNumber('tahapanId');
});
