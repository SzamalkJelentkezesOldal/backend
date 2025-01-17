<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JelentkezoRequest extends FormRequest
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
            'jelentkezo' => 'required|array',
            'jelentkezo.nev' => 'required|string|max:255',
            'jelentkezo.email' => 'required|email|max:255|unique:jelentkezos,email',
            'jelentkezo.tel' => 'required|string|max:15',
            'jelentkezes' => 'required|array',
            'jelentkezes.kivalasztottSzakok' => 'required|array|min:1',
            'jelentkezes.kivalasztottSzakok.*' => 'required|integer|exists:szaks,id',
            'portfolio.portfolioSzakok' => 'nullable|array',
            'portfolio.portfolioSzakok.*.szak_id' => 'integer|exists:szaks,id',
            'portfolio.portfolioSzakok.*.portfolio_url' => 'url',
        ];
    }

    public function messages()
    {
        return [
            'jelentkezo.required' => 'A jelentkező mező kitöltése kötelező.',
            'jelentkezo.array' => 'A jelentkező mezőnek tömbnek kell lennie.',
            'jelentkezo.nev.required' => 'A név mező kitöltése kötelező.',
            'jelentkezo.nev.string' => 'A név mezőnek szövegnek kell lennie.',
            'jelentkezo.nev.max' => 'A név mező nem lehet hosszabb 255 karakternél.',
            'jelentkezo.email.required' => 'Az email mező kitöltése kötelező.',
            'jelentkezo.email.email' => 'Az email mezőnek érvényes email címnek kell lennie.',
            'jelentkezo.email.max' => 'Az email mező nem lehet hosszabb 255 karakternél.',
            'jelentkezo.email.unique' => 'Ez az email cím már foglalt.',
            'jelentkezo.tel.required' => 'A telefonszám mező kitöltése kötelező.',
            'jelentkezo.tel.string' => 'A telefonszám mezőnek szövegnek kell lennie.',
            'jelentkezo.tel.max' => 'A telefonszám mező nem lehet hosszabb 15 karakternél.',
            'jelentkezes.required' => 'A jelentkezés mező kitöltése kötelező.',
            'jelentkezes.array' => 'A jelentkezés mezőnek tömbnek kell lennie.',
            'jelentkezes.kivalasztottSzakok.required' => 'Legalább egy szakot ki kell választani.',
            'jelentkezes.kivalasztottSzakok.array' => 'A kiválasztott szakok mezőnek tömbnek kell lennie.',
            'jelentkezes.kivalasztottSzakok.min' => 'Legalább egy szakot ki kell választani.',
            'jelentkezes.kivalasztottSzakok.*.required' => 'A kiválasztott szakok mező minden elemének kötelezőnek kell lennie.',
            'jelentkezes.kivalasztottSzakok.*.integer' => 'A kiválasztott szakok mező minden elemének egész számnak kell lennie.',
            'jelentkezes.kivalasztottSzakok.*.exists' => 'A kiválasztott szakok mező minden elemének létező szaknak kell lennie.',
            'portfolio.portfolioSzakok.array' => 'A portfólió szakok mezőnek tömbnek kell lennie.',
            'portfolio.portfolioSzakok.*.szak_id.integer' => 'A portfólió szakok mező minden elemének egész számnak kell lennie.',
            'portfolio.portfolioSzakok.*.szak_id.exists' => 'A portfólió szakok mező minden elemének létező szaknak kell lennie.',
            'portfolio.portfolioSzakok.*.portfolio_url.url' => 'A portfólió URL mezőnek érvényes URL-nek kell lennie.',
        ];
    }
}
