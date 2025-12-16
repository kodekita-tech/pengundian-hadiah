<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $eventId = $this->route('event')?->id;
        $user = $this->user();
        $isAdminOpd = $user && $user->role === 'admin_opd';

        $rules = [
            'nm_event' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'pendaftaran_dibuka', 'pendaftaran_ditutup', 'pengundian', 'selesai'])],
            'tgl_mulai' => ['nullable', 'date_format:Y-m-d H:i'],
            'tgl_selesai' => ['nullable', 'date_format:Y-m-d H:i', 'after:tgl_mulai'],
            'deskripsi' => ['nullable', 'string'],
        ];

        // Untuk admin_opd, opd_id tidak perlu divalidasi karena akan di-set dari user
        // Untuk superadmin/developer, opd_id wajib dan harus exists
        if (!$isAdminOpd) {
            $rules['opd_id'] = ['required', 'exists:opd,id'];
        } else {
            // Untuk admin_opd, tetap validasi jika ada (dari hidden input)
            $rules['opd_id'] = ['nullable', 'exists:opd,id'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nm_event.required' => 'Nama event wajib diisi.',
            'opd_id.required' => 'OPD wajib dipilih.',
            'opd_id.exists' => 'OPD yang dipilih tidak valid.',
            'status.required' => 'Status event wajib dipilih.',
            'tgl_selesai.after' => 'Tanggal penutupan harus setelah tanggal pembukaan.',
        ];
    }
}

