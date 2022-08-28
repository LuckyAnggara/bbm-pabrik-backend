<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductionOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sequence'=> $this->sequence,
            'pic_name' => $this->pic_name,
            'customer_name' => $this->customer_name,
            'status'=> $this->status,
            'notes'=> $this->notes,
            'target_date'=> $this->target_date,
            'created_by'=> $this->created_by,
            'created_at' => $this->created_at->format('m/d/Y'),
            'updated_at' => $this->updated_at->format('m/d/Y'),
        ];
    }
}
