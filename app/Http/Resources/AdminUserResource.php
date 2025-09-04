<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AdminUserResource extends JsonResource
{
    /**
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        
        $imagePath = $this->image; 
        $imageUrl  = $imagePath ? Storage::url($imagePath) : null;

       
        if (!$imageUrl) {
        
            $imageUrl = null;
        }

        return [
            'id'        => (int) $this->id,
            'name'      => (string) $this->name,
            'image_url' => $imageUrl,
        ];
    }
}
