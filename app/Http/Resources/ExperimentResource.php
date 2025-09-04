<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ExperimentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'image_url'   => $this->image ? Storage::url($this->image) : null,
            'status'      => $this->status,
            'duration'    => $this->duration,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
