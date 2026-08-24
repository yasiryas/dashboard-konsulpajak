<?php

namespace App\Http\Requests\Admin;

use App\Enums\JenisKlien;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Admin;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nama_entitas' => ['required', 'string', 'max:255'],
            'jenis_klien' => ['required', Rule::enum(JenisKlien::class)],
            'npwp' => ['nullable', 'string', 'max:20'],
            'package_id' => ['nullable', 'integer', 'exists:service_packages,id'],
        ];
    }
}
