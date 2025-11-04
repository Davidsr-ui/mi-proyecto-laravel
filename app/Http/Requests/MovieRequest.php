<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'director' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'genre' => 'required|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:10',
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio',
            'description.required' => 'La descripción es obligatoria',
            'director.required' => 'El director es obligatorio',
            'year.required' => 'El año es obligatorio',
            'year.min' => 'El año debe ser mayor a 1900',
            'year.max' => 'El año no puede ser mayor a ' . (date('Y') + 5),
            'genre.required' => 'El género es obligatorio',
            'rating.numeric' => 'La calificación debe ser un número',
            'rating.min' => 'La calificación mínima es 0',
            'rating.max' => 'La calificación máxima es 10',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif',
            'image.max' => 'La imagen no puede pesar más de 2MB'
        ];
    }
}
