<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{

    public function index(Request $request)
    {
        $objects = Warehouse::all();
        $items =[];
        foreach ($objects as $obj) {
            $items[] = [
                'id' => $obj->id,
                'name'=>$obj->name,
                'actions'=>[
                    'actions'=>true,
                    'edit'=>true,
                    'delete'=>true,
                    'id' => $obj->id,
                    'delete_route' => '/products/warehouse/delete',
                ],
            ];
        }
        $list = [
            'headers'=>[
                [
                    'text'=>'Name','value'=>'name',
                ],
                [
                    'text'=>'Actions','value'=>'actions','align'=>'right',
                ]
            ],
            'items' => $items,
        ];
        return response()->json([
            'list'=> $list
        ]);

    }
    public function add(){
        
    }
    public function save(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('warehouses'),
            ],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $obj = new Warehouse;
        $obj->name = trim($request->name);
        $obj->slug = Str::slug($request->name,'_');
        $obj->save();
        return response()->json([
            'message'=>'success'
        ]);
    }
    public function edit(Request $request, $id){
        
    }
    public function update(Request $request,$id)
    {
        
    }
    public function delete(Request $request)
    {
        $ids = explode(",",$request->delete_ids);
        foreach ($ids as $id) {
            $obj =  Product::find($id);
            $obj->categories()->detach();
        }
        Product::destroy($ids);
        return response()->json([
            'message'=>'success',
        ]);
    }
}
