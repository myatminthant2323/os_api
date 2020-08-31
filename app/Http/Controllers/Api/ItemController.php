<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Item;
use App\Subcategory;
use App\Brand;
use Illuminate\Http\Request;
use App\Http\Resources\ItemResource;

class ItemController extends Controller
{


	public function __construct($value='') // authentication
	{
		$this->middleware('auth:api')->except('index','filter','search','search_by_name'); // for api use ('auth:api')
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::all();
        dd($items);
        // return $items;
        return response()->json([
            "status" => "ok",
            "totalResults" => count($items),
            "items" => ItemResource::collection($items)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'codeno' => 'required',
            'name' => 'required',
            'photo' => 'required',
            'price' => 'required',
            'discount' => 'required',
            'description' => 'required',
            'brand' => 'required',
            'subcategory' => 'required',

        ]);

        // File Upload
        $imageName = time().'.'.$request->photo->extension();

        $request->photo->move(public_path('backendtemplate/itemimg'),$imageName);
        $myfile = 'backendtemplate/itemimg/'.$imageName;

        // Store Data
        $item = new Item;
        $item->codeno = $request->codeno;
        $item->name = $request->name;
        $item->photo = $myfile;
        $item->price = $request->price;
        $item->discount = $request->discount;
        $item->description = $request->description;
        $item->brand_id = $request->brand;
        $item->subcategory_id = $request->subcategory;

        $item->save();


        $status = 1;
        return new ItemResource($item);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
         
        // return $item;
        return new ItemResource($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
    }

    public function filter($sid,$bid)
    {
        $items = array();
        if ($sid && $bid){
            $items = Item::where('subcategory_id',$sid)->where('brand_id', $bid)->get();
        }else{
            $items = Item::where('subcategory_id',$sid)->or_where('brand_id', $bid)->get();
        }

        // return new ItemResource($items);
        return $items;
    }

    public function search_by_name(Request $request)
    {
        $exit = true;
        $items = array();
        $item = $request->query('item');
        $subcategory = $request->query('subcategory');
        // dd($subcategory);
        $brand = $request->query('brand');

        $brands = Brand::where('name', 'LIKE', '%'.$brand.'%')->get();
        $subcategories = Subcategory::where('name', 'LIKE', '%'.$subcategory.'%')->get();
        $query = Item::query();
        if($subcategory){
            // dd($subcategories);
            if(count($subcategories)>0){
                // dd($subcategories);
                foreach ($subcategories as $s_key => $s_value) {
                    // dd($value);
                    // dd($brands);
                    if($brand){
                        if(count($brands)>0){
                            foreach ($brands as $b_key => $b_value) {
                                if($s_key == 0 && $b_key == 0){
                                    $query->where('subcategory_id', $s_value->id);
                                }else{
                                    $query->orWhere('subcategory_id', $s_value->id);
                                }
                                $query->where('brand_id', $b_value->id);
                                if ($item) {
                                    $query->where('name', 'LIKE', '%'.$item.'%');
                                }
                            }
                        }else{
                            $exit = false;
                        }
                    }
                    if($s_key == 0){
                        $query->where('subcategory_id', $s_value->id);
                    }else{
                        $query->orWhere('subcategory_id', $s_value->id);
                    }

                    if ($item) {
                        $query->where('name', 'LIKE', '%'.$item.'%');
                    }
                    
                    
                }
            }
        }

        ///////////////////End Subcategory/////////////////////////


        if($brand){
            // dd($subcategories);
            if(count($brands)>0){
                // dd($subcategories);
                foreach ($brands as $b_key => $b_value) {
                    // dd($value);
                    // dd($brands);

                    // dd(count($subcategories));
                    if($subcategory){
                        if(count($subcategories)>0){
                            foreach ($subcategories as $s_key => $s_value) {
                                if($b_key == 0 && $s_key == 0){
                                    $query->where('brand_id', $b_value->id);
                                }else{
                                    $query->orWhere('brand_id', $b_value->id);
                                }
                                $query->where('subcategory_id', $s_value->id);
                                if ($item) {
                                    $query->where('name', 'LIKE', '%'.$item.'%');
                                }
                            }
                        }else{
                            $exit = false;
                        }
                    }else{
                        if($b_key == 0){
                            $query->where('brand_id', $b_value->id);
                        }else{
                            $query->orWhere('brand_id', $b_value->id);
                        }

                        if ($item) {
                            $query->where('name', 'LIKE', '%'.$item.'%');
                        }
                    }
                    
                    
                }
                // dd($query);
            }
        }

        ////////////////////////End Brand///////////////////

        if ($item) {
            $query->where('name', 'LIKE', '%'.$item.'%');
        }

        ///////////////////////End Item////////////////////

        if($exit){
            $items = $query->get(); 
        }else{
            $items = array(); 
        }
        
        // dd($items);


        return response()->json([
            "status" => "ok",
            "totalResults" => count($items), 
            "items" => ItemResource::collection($items)
        ]);

    }

    public function search(Request $request)
    {
        $items = array();
        $sid = $request->get('subcategory');
        $bid = $request->get('bid');
        $name = $request->get('name');
        if ($sid && $bid){
            if ($name){
                $items = Item::where('subcategory_id',$sid)->where('brand_id', $bid)->where('name', 'LIKE', "%{$name}%")->get();
            }else{
                $items = Item::where('subcategory_id',$sid)->where('brand_id', $bid)->get();
            }
            
        }elseif($sid || $bid){
            if ($name){
                $items = Item::where('subcategory_id',$sid)->orWhere('brand_id', $bid)->where('name', 'LIKE', "%{$name}%")->get();
            }else{
                $items = Item::where('subcategory_id',$sid)->orWhere('brand_id', $bid)->get();
            }
            
        }else{
            if ($name){
                $items = Item::where('name', 'LIKE', "%{$name}%")->get();
            }else{
                $items = Item::all();
            }
            
        }
        return response()->json([
            "status" => "ok",
            "totalResults" => count($items), 
            "items" => ItemResource::collection($items)
        ]);
    }
}
