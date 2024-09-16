<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'articles' => 'required|array',
            'articles.*.libelle' => 'required|string',
            'articles.*.prix' => 'required|numeric',
            'articles.*.quantite' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'articles.required' => 'Le champ articles est requis.',
            'articles.array' => 'Les articles doivent être un tableau.',
            'articles.*.libelle.required' => 'Le libelle de l\'article est requis.',
            'articles.*.prix.required' => 'Le prix de l\'article est requis.',
            'articles.*.quantite.required' => 'La quantité de l\'article est requise.',
        ];
    }
}
