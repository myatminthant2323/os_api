<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ItemResource;
use App\Item;
use App\User;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = 'order';

    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
         'order_id' => $this->id,
         'order_voucherno' => $this->voucherno,
         'order_date' => $this->orderdate,
         'order_total' => $this->total,
         'order_user' => new UserResource(User::find($this->user_id)),
         'order_items' => ItemResource::collection($this->items),
         'created_at' => $this->created_at,
         'updated_at' => $this->updated_at,

     ];
    }
}
