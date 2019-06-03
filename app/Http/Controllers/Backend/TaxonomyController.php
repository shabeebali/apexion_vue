<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Taxonomy\Models\Category;
use App\Modules\Taxonomy\Models\CategoryType;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use TorMorten\Eventy\Facades\Events as Eventy;
class TaxonomyController extends Controller
{
    protected $searcheable =['name'];

    public function index(Request $request){
        $headers=[];
        $select_array = ['id','name','type_id','code'];
        $list_terms = helper('apex')->get_list_terms($request,'category',$select_array,$this->searcheable);
        if($request->get('search')){
            $data = Category::where($list_terms['search'])->get();
        }
        else{
            $data = Category::all();
        }
        $list = helper('apex')->perform_filtering($data,$list_terms['rpp'],$list_terms['page'],$select_array,$request,'category',Category::class);
        return response()->json(
            [
                'items'=>$list['items'],
                'headers'=>$list_terms['headers'],
                'total'=>$list['total'],
                'fields'=>'',
                'filterables'=>$list['filterables'],
                'filtered' => $list['filtered'],
            ]
        );
    }
    public function add()
    {
        $cat_types = CategoryType::all();
        $category_types = [];
        foreach ($cat_types as $type) {
            $category_types[]=['text' => $type->name, 'value' => $type->id];
        }
        $formdata =[
            'name'=>[
                'value'=>'', 'error'=>''
            ],
            'code' =>[
                'value'=>'', 'error'=>''
            ],
            'type_id' =>[
                'value' => $category_types[0]['value']
            ],
        ];
        return response()->json([
            'formdata' => $formdata, 'category_type' => $category_types
        ]);
    }
    public function save(Request $request)
    {
        $category_type = CategoryType::find($request->type_id);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('categories')->where('type_id',$request->type_id),
            ],
            'code' => [
                Rule::requiredIf($category_type->in_pc),
                'size:'.$category_type->code_length,
                Rule::unique('categories')->where('type_id',$request->type_id),
            ]
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $obj = new Category;
        $obj->name = trim($request->name);
        $obj->slug = Str::slug($obj->name);
        $obj->type_id = $request->type_id;
        $obj->code = $request->code;
        $obj->save();
        return response()->json([
            'message'=> 'success'
        ]);
    }
    public function edit($id)
    {
        $obj = Category::find($id);
        return response()->json([
            'formdata'=>$obj->only(['name','code','type_id'])
        ]);
    }
    public function update(Request $request, $id)
    {
        $obj = Category::find($id);
        $category_type = CategoryType::find($request->type_id);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('categories')->where('type_id',$request->type_id)->ignore($obj->id),
            ],
            'code' => [
                'required',
                'size:'.$category_type->code_length,
                Rule::unique('categories')->where('type_id',$request->type_id)->ignore($obj->id),
            ]
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $obj->name = trim($request->name);
        $obj->slug = Str::slug($obj->name);
        $obj->type_id = $request->type_id;
        $obj->code = $request->code;
        $obj->save();
        return response()->json([
            'message'=> 'success'
        ]);
    }
    public function delete(Request $request)
    {
        $ids = explode(",",$request->delete_ids);
        Category::destroy($ids);
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function type_list()
    {
        $objects = CategoryType::select('id','name')->get();
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
    public function type_add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:category_types',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $object = new CategoryType;
        $object->name = trim($request->name);
        $object->slug = Str::slug($object->name);
        $object->code_length = $request->code_length;
        $object->code_type = $request->code_type;
        $object->autogen = $request->autogen;
        $object->in_pc = $request->in_pc;
        $object->save();
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function type_edit($id)
    {
        $obj = CategoryType::find($id);
        return response()->json([
            'data'=>$obj->only(['name','code_length','code_type','autogen','in_pc'])
        ]);
    }
    public function type_update(Request $request,$id)
    {
        $object = CategoryType::find($id);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('category_types')->ignore($object->id),
            ]
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }

        $object->name = trim($request->name);
        $object->slug = Str::slug($object->name);
        $object->save();
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function get_filterables()
    {
        $filterables = helper('apex')->get_filterables('category',Category::class);
        return response()->json(['filterables'=>$filterables]);
    }
}
