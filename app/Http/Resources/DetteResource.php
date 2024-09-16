<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'client' => new ClientResource($this->whenLoaded('client')),
            'articles' => ArticleResource::collection($this->whenLoaded('articles')),
            'date_dette' => $this->date_dette,
            'created_at' => $this->created_at,
        ];
    }
}
