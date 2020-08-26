<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource; //
use App\Category; // to find data in category table

class SubcategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
        "subcategory_id" => $this->id,
        "subcategory_name" => $this->name,
        "category" => new CategoryResource(Category::find($this->category_id))
      ];
    }
}
