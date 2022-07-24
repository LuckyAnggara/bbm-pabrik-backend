<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use App\Http\Resources\ItemTypeResource;
use App\Http\Resources\ItemUnitResource;
use App\Models\ItemType;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'id' =>$this->id,
            'name' =>  Str::upper($this->name),
            'type_id' => $this->type_id,
            'unit_id' => $this->unit_id,
            'warehouse_id' => $this->warehouse_id,
            'type'=> $this->type,
            'unit'=> $this->unit,
            // 'mutation'=> $this->mutation,
            'created_by' => $this->created_by,
            'user'=> $this->user,
            'created_at' => $this->created_at->format('m/d/Y'),
            'updated_at' => $this->updated_at->format('m/d/Y'),
        ];
    }
}
