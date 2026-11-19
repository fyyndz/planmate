<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul'              => ['required', 'string', 'max:255'],
            'aktivitasId'        => ['nullable', 'integer', 'exists:aktivitas,id'],
            'deadline'           => ['nullable', 'date'],
            'deskripsi'          => ['nullable', 'string'],
            'progres'            => ['nullable', 'integer', 'min:0', 'max:100'],
            'targetKecil'        => ['nullable', 'array'],
            'targetKecil.*.text' => ['required', 'string'],
            'targetKecil.*.done' => ['nullable', 'boolean'],
        ];
    }

    public function atribut(): array
    {
        return [
            'judul'        => $this->input('judul'),
            'aktivitas_id' => $this->input('aktivitasId') ?: null,
            'deadline'     => $this->input('deadline') ?: null,
            'deskripsi'    => $this->input('deskripsi') ?: null,
        ];
    }
}