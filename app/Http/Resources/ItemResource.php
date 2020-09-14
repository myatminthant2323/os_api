<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SubcategoryResource; //
use App\Http\Resources\BrandResource;
use App\Subcategory;
use App\Brand;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = 'item'; // to wrap data from data

    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
        "item_id" => $this->id,
        "item_codeno" => $this->codeno,
        "item_name" => $this->name,
        "item_price" => $this->price,
        'qty' => $this->whenPivotLoaded('order_detail', function () {
                return $this->pivot->qty;
            }),
        "item_photo" => url($this->photo),
        "item_discount" => $this->discount,
        "item_description" => $this->description,
        "subcategory" => new SubcategoryResource(Subcategory::find($this->subcategory_id)),
        "brand" => new BrandResource(Brand::find($this->brand_id)),

      ];
    }
}
