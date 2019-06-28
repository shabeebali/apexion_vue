<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Taxonomy\Models\Category;
use App\Modules\Taxonomy\Models\CategoryType;
use App\Modules\ProductCode\Models\CodeOrder;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use TorMorten\Eventy\Facades\Events as Eventy;
use App\Exports\CategoriesExport;
use App\Imports\CategoriesImport;
class TaxonomyController extends Controller
{
    protected $searcheable =['name'];

    public function index(Request $request){
        $user = \Auth::user();
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
                'addflag' => $user->can('create_category') ? 1 : 0,
                'exportflag' => 1,
                'importflag' => $user->can('create_category') && $user->can('edit_category') ? 1: 0,
            ]
        );
    }
    public function add()
    {
        $cat_types = CategoryType::all();
        $category_types = [];
        foreach ($cat_types as $type) {
            $category_types[]=['text' => $type->name, 'value' => $type->id, 'autogen' => $type->autogen? 1: 0, 'next_code'=>$type->next_code];
        }
        $formdata =[
            'name'=>[
                'value'=>'', 'error'=>''
            ],
            'code' =>[
                'value'=>'', 'error'=>'',
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
        $obj->slug = Str::slug($obj->name,'_');
        $obj->type_id = $request->type_id;
        if($category_type->autogen)
        {
            $obj->code = strtoupper($category_type->next_code);
        }
        else
        {
            $obj->code = strtoupper($request->code);
        }
        $obj->save();
        helper('apex')->update_next_code($category_type);
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
        $obj->slug = Str::slug($obj->name,'_');
        $obj->type_id = $request->type_id;
        $obj->code = strtoupper($request->code);
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
                    'delete_route' => '/products/category_type/delete',
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
        $object->slug = Str::slug($object->name,'_');
        $object->code_length = $request->code_length;
        $object->code_type = $request->code_type;
        $object->autogen = $request->autogen;
        $object->in_pc = $request->in_pc;
        $next_code='';
        if($request->autogen && $request->in_pc)
        {
            $ct_arr = explode('-',$request->code_type);
            foreach ($ct_arr as $key => $value) {
                if($value == 'alpha'){
                    $next_code = $next_code.'A';
                }
                else{
                    $next_code = $next_code.'0';
                }
            }
            $object->next_code = $next_code;
        }
        $object->save();
        $code_order = new CodeOrder;
        if($request->in_pc){
            $code_order->type_id = $object->id;
            $code_order->order = CodeOrder::max('order')+1;
            $code_order->save();
        }
        $cat = new Category;
        $cat->name = 'None';
        $cat->slug = Str::slug($cat->name,'_');
        $cat->type_id = $object->id;
        $cat->code = str_pad('', $object->code_length,"N");

        $cat->save();
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
        $object->slug = Str::slug($object->name,'_');
        $object->save();
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function type_delete(Request $request)
    {
        $id = $request->id;
        $obj = CategoryType::find($id);
        $in_pc = $obj->in_pc;
        $categories = $obj->categories()->get();
        $cat_ids = [];
        foreach ($categories as $category) {
            $cat_ids[]=$category->id;
        }
        Category::destroy($cat_ids);
        CategoryType::destroy($id);
        if($in_pc){
            CodeOrder::where('type_id',$id)->delete();
            $code_orders = CodeOrder::orderBy('order','asc')->get();
            $count = 1;
            foreach ($code_orders as $code_order) {
                $code_order->order = $count;
                $code_order->save();
                $count++;
            }
        }
    }
    public function get_filterables()
    {
        $filterables = helper('apex')->get_filterables('category',Category::class);
        return response()->json(['filterables'=>$filterables]);
    }
    public function code_order()
    {
        $code_orders = CodeOrder::orderBy('order','asc')->get();
        $data=[];
        foreach ($code_orders as $code_order) {
            $data[] = [
                'id' => $code_order->id,
                'name' => CategoryType::find($code_order->type_id)->name,
                'order' => $code_order->order,
            ];
        }
        return response()->json([
            'data'=>$data
        ]);
    }
    public function save_code_order(Request $request)
    {
        $ids = explode(",", $request->ids);
        $count = 1;
        foreach ($ids as $id) {
            $code_order = CodeOrder::find($id);
            $code_order->order = $count;
            $count++;
            $code_order->save();
        }
    }
    public function import(Request $request)
    {
        //dd($request->toArray());
        $type_id = $request->type_id;
        $file = $request->file('file');
        $method =  $request->method;
        //dd($file->extension());
        if($file->extension() != 'xlsx' && $file->extension() != 'zip')
        {
            return response()->json([
                'status' => 'file_failed',
                'message' => 'Error: The uploaded file is not valid. Please try again'
            ]);
        }
        else{
            try {
                $import = new CategoriesImport($type_id,$method);
                $import->import($request->file('file'));
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

                 $failures = $e->failures();
                
                 foreach ($failures as $failure) {
                    $msg = $failure->errors();
                    $messages[$failure->row()][$failure->attribute()]['message'] = $msg[0];
                 }
                 return response()->json([
                    'status' => 'failed',
                    'messages' => $messages
                ]);
            }
           return response()->json([
                'status' => 'success',
                'message' => 'Import Completed successfully'
            ]);
        }
    }
    public function export()
    {
        return (new CategoriesExport)->download('categories.xlsx');
    }
    public function types()
    {
        $obj = CategoryType::all();
        $data=[];
        foreach ($obj as $type) {
            $data[] = [
                'value'=> $type->id,
                'text' => $type->name,
            ];
        }
        return response()->json([
            'data'=>$data
        ]);
    }
}
