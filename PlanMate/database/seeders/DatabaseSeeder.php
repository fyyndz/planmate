<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo (sama seperti kotak "Akun Demo" di halaman login)
        $user = User::firstOrCreate(
            ['email' => 'mahasiswa@student.ac.id'],
            [
                'name'     => 'Mahasiswa PlanMate',
                'username' => 'mahasiswa',
                'password' => 'password123',
                'jurusan'  => 'Teknik Informatika',
                'bio'      => 'Semangat belajar & mengerjakan tugas perkuliahan!',
            ]
        );

        // Contoh goals untuk akun demo
        if (Goal::where('user_id', $user->id)->doesntExist()) {
            $g1 = Goal::create([
                'user_id'      => $user->id,
                'aktivitas_id' => 1,
                'judul'        => 'Selesaikan Praktikum Antarmuka Pengguna Modul 1-4',
                'deskripsi'    => 'Menuntaskan seluruh modul praktikum, integrasi fitur, dan laporan akhir.',
                'deadline'     => now()->addDays(5)->startOfDay(),
                'progres'      => 75,
            ]);
            foreach ([
                ['Review wireframe & UI layout', true],
                ['Perbaiki alur autentikasi & validasi data', true],
                ['Implementasi manajemen target & progres', true],
                ['Finalisasi laporan praktikum & presentasi', false],
            ] as [$text, $done]) {
                $g1->tahapan()->create(['text' => $text, 'done' => $done]);
            }

            $g2 = Goal::create([
                'user_id'      => $user->id,
                'aktivitas_id' => null,
                'judul'        => 'Target Belajar Mandiri: Menguasai SQL & Query Optimization',
                'deskripsi'    => 'Mempelajari query kompleks, relasi normalisasi, dan indexing basis data.',
                'deadline'     => now()->addDays(14)->startOfDay(),
                'progres'      => 50,
            ]);
            foreach ([
                ['Belajar relasi tabel & normalisasi 1NF-3NF', true],
                ['Latihan query JOIN, GROUP BY, dan HAVING', true],
                ['Pahami query execution plan & indexing', false],
                ['Latihan simulasi studi kasus transaksi kasir', false],
            ] as [$text, $done]) {
                $g2->tahapan()->create(['text' => $text, 'done' => $done]);
            }
        }
    }
}
