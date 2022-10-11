<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterExitResource extends JsonResource
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
            'mutation_code' => $this->mutation_code,
            'type' => $this->type,
            'detail' => $this->detail,
            'notes' => $this->notes,
            'admin_name' => $this->admin_name,
            'driver_name' => $this->driver_name,
            'no_pol' => $this->no_pol,
            'data_date' => $this->data_date,
            'created_by' => $this->created_by,
            'user' => $this->user,
        ];
    }
}
