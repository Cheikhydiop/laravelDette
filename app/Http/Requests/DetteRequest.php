<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetteRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Autoriser toutes les requêtes pour cet exemple
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'montant' => 'required|numeric|min:0',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|numeric|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement.montant' => 'nullable|numeric|max:' . $this->input('montant'),
        ];
    }

    /**
     * Obtenir les messages de validation personnalisés.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'articles.array' => 'Les articles doivent être un tableau.',
            'articles.min' => 'Il doit y avoir au moins un article.',
            'articles.*.articleId.exists' => 'L\'article spécifié n\'existe pas.',
            'articles.*.qteVente.min' => 'La quantité de vente doit être positive.',
            'paiement.montant.max' => 'Le montant du paiement ne peut pas dépasser le montant de la dette.',
        ];
    }
}
