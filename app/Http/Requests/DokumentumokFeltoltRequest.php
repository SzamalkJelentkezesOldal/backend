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
            'adoazonosito' => 'required|array|min:1',
            'adoazonosito.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'taj' => 'required|array|min:1',
            'taj.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'szemelyi_elso' => 'required|array|min:1',
            'szemelyi_elso.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'szemelyi_hatso' => 'required|array|min:1',
            'szemelyi_hatso.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'lakcim_elso' => 'required|array|min:1',
            'lakcim_elso.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'lakcim_hatso' => 'required|array|min:1',
            'lakcim_hatso.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'onarckep' => 'required|array|min:1',
            'onarckep.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'nyilatkozatok' => 'required|array|min:1',
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
