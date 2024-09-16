<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transforme la ressource en un tableau.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'libeller' => $this->libeller,
            'quantite' => $this->quantite,
            'prix' => $this->prix,


            // 'pivot' => [
            //     'dette_id' => $this->pivot->dette_id,
            //     'article_id' => $this->pivot->article_id,
            //     'quantite' => $this->pivot->quantite,
            //     'prix_vente' => $this->pivot->prix_vente,
            // ],
        ];
    }
}
