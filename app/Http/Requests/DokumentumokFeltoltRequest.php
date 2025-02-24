<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DokumentumokFeltoltRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'adoazonosito' => 'nullable|array',
            'adoazonosito.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'taj' => 'nullable|array',
            'taj.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'szemelyi_elso' => 'nullable|array',
            'szemelyi_elso.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'szemelyi_hatso' => 'nullable|array',
            'szemelyi_hatso.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'lakcim_elso' => 'nullable|array',
            'lakcim_elso.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'lakcim_hatso' => 'nullable|array',
            'lakcim_hatso.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'onarckep' => 'nullable|array',
            'onarckep.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'nyilatkozatok' => 'nullable|array',
            'nyilatkozatok.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'erettsegik' => 'sometimes|array',
            'erettsegik.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tanulmanyik' => 'sometimes|array',
            'tanulmanyik.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'specialisok' => 'sometimes|array',
            'specialisok.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
    
}
