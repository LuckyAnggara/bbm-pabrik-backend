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
            'sequence' => $this->sequence,
            'pic_name' => $this->pic_name,
            'customer_name' => $this->customer_name,
            'status' => $this->status,
            'notes' => $this->notes,
            'shift' => $this->shift,
            'input' => $this->input,
            'output' => $this->output,
            'machine' => $this->machine,
            'overhead' => $this->overhead,
            'pegawai' => $this->pegawai,
            'timeline' => $this->timeline,
            'target_date' => $this->target_date->format('Y-m-d'),
            'order_date' => $this->order_date->format('Y-m-d'),
            'created_by' => $this->created_by,
            'user' => $this->user,
            'created_at' => $this->created_at->format('m/d/Y'),
        ];
    }
}
