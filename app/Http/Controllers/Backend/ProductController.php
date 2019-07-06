<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Warehouse;
use App\Modules\Product\Models\Pricelist;
use App\Modules\Taxonomy\Models\Category;
use App\Modules\Taxonomy\Models\CategoryType;
use App\Modules\ProductCode\Models\CodeOrder;
use App\Modules\Product\Models\ProductListSettings;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Modules\Product\Events\SampleEvent;
use TorMorten\Eventy\Facades\Events as Eventy;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
class ProductController extends Controller
{
    protected $searcheable =['name'];

    public function getMenu()
    {
        $user = \Auth::user();
        $items = [];
        if($user->can('list_products')){
            $items[] = [
                'text'=>'Products',
                'route'=>'/products/list',
                'icon'=>'list_alt',
            ];
        }
        if($user->can('list_category')){
            $items[] = [
                'text'=>'Categories',
                'route'=>'/products/categories/list',
                'icon'=>'category',
            ];
        }
        if($user->can('access_product_settings')){
            $items[] = [
                'text'=>'Settings',
                'route'=>'/products/settings',
                'icon'=>'settings',
            ];
        }
        return response()->json([
            'items' => $items
        ]);
    }
    public function index(Request $request)
    {
        $user=\Auth::user();
        $select_array = ['products.id','name','mrp','general_selling_price'];  //
        $list_settings = ProductListSettings::select('value')->where('user_id',$user->id)->first();
        if($list_settings)
        {
            $select_array = explode(",",$list_settings->value);
            if(in_array('id', $select_array))
            {
                $select_array[array_search('id', $select_array)] = 'products.id';
            }
        }
        $list_terms = helper('apex')->get_list_terms($request,'product',$select_array,$this->searcheable);
        $data = Product::select($select_array);
        if($request->pending)
        {
            $data = $data->where('publish',0);
        }
        elseif($request->tally)
        {
            $data = $data->where('tally',0)->where('publish',1);
        }
        else
        {
            $data = $data->where('publish',1);
        }
        if($request->get('search')){
            $data = $data->where($list_terms['search']);
        }
        $list = helper('apex')->perform_filtering($data,$select_array,$request,'product',Product::class);
        return response()->json(
            [
                'items'=>$list['items'],
                'headers'=>$list_terms['headers'],
                'total'=>$list['total'],
                'fields'=>$list_terms['fields'],
                'filterables'=>$list['filterables'],
                'filtered' => $list['filtered'],
                'addflag' => $user->can('create_product') ? 1:0, 
                'exportflag' => 1,
                'importflag' => $user->can('create_product') && $user->can('edit_product') ? 1: 0,
            ]
        );
    }
    public function pending_count()
    {
        $user = \Auth::user();
        
        return response()->json([
            'count'=> $user->can('approve_product') ? Product::where('publish',0)->get()->count() : 0,
        ]);
    }
    public function tally_count()
    {
        $user = \Auth::user();
        return response()->json([
            'count'=> $user->can('sync_tally') ? Product::where([['tally','=',0],['publish','=',1]])->get()->count() : 0
        ]);
    }
    public function view(Request $request,$id)
    {
        $category_types = CategoryType::all();
        $warehouses = Warehouse::all();
        $pricelists = Pricelist::all();
        $obj = Product::find($id);
        $data=[];
        $fields = helper('apex')->get_fields('product');
        $temp =[];
        foreach ($fields as $key => $value) {
            if(isset($value['avoid']) && $value['avoid']){
                continue;
            }
            $temp[] = [
                'key'=>$value['text'],
                'value'=> $obj->$key
            ];
        }
        $temp[] = [
            'key'=>'SKU',
            'value'=>$obj->sku
        ];
        $cats = $obj->categories()->get();
        foreach ($category_types as $type) {
            $temp[] = [
                'key'=>$type->name,
                'value'=>$obj->categories()->where('type_id',$type->id)->first()->name,
            ];
        }
        $data['items'][]=[
            'name'=>'Details',
            'list'=>$temp,
        ];
        $temp=[];
        $pricelists = Pricelist::all();
        foreach ($pricelists as $pricelist) {
            $v = $obj->pricelists()->where('pricelist_id',$pricelist->id)->first();
            $temp[] = [
                'key'=>$pricelist->name.' Margin',
                'value'=>$v->pivot->value
            ];
            $temp[] = [
                'key'=>$pricelist->name.' Price',
                'value'=>number_format( ceil( ($obj->landing_price*(( $v->pivot->value/100 )+1))*(($obj->gst/100)+1) ),2,'.',',' ),
            ];
        }
        foreach ($warehouses as $warehouse) {
            $v = $obj->warehouses()->where('warehouse_id',$warehouse->id)->first();
            $temp[] =[
                'key'=>'Warehouse '.$warehouse->name.' Stock',
                'value'=> $v->pivot->stock,
            ];
        }
        $data['items'][] = [
            'name'=>'Pricelist & Stock',
            'list'=>$temp,
        ];
        return response()->json([
            'data'=>[
                'name'=>$obj->name,
                'items' => $data['items'],
            ],
        ]);
    }
    public function add(){
        $user = \Auth::user();
        $category_types = CategoryType::all();
        $pc_category_types = [];
        $npc_category_types = [];
        if($category_types->count() > 0)
        {
            foreach ($category_types as $category_type) {
                $options=[];
                $temp = Category::where('type_id',$category_type->id)->get();
                foreach ($temp as $it) {
                    $options[] = ['text'=>$it->name,'value'=>$it->id];
                }
                if($category_type->in_pc == 1)
                {
                    $pc_category_types[$category_type->slug] = [
                        'label' => $category_type->name,
                        'options' => $options,
                        'value' => '',
                        'error' => ''
                    ];
                }
                else
                {
                    $npc_category_types[$category_type->slug] = [
                        'label' => $category_type->name,
                        'options' => $options,
                        'value' => '',
                        'error' => ''
                    ];
                }
            }
        }
        $pricelists = Pricelist::all();
        $pl=[];
        if($pricelists->count() >0)
        {
            foreach ($pricelists as $pricelist) {
                $pl[$pricelist->slug] = [
                    'id'=>$pricelist->id,
                    'name'=>$pricelist->name,
                    'value'=> 0,
                    'error'=> ''
                ];
            }
        }
        $warehouses =  Warehouse::all();
        $wh = [];
        if($warehouses->count() > 0)
        {
            foreach ($warehouses as $warehouse) {
                $wh['warehouse_'.$warehouse->slug] = [
                    'id'=>$warehouse->id,
                    'name'=>$warehouse->name,
                    'value'=> 0,
                    'error'=> '',
                ];
            }
        }
        return response()->json([
            'pc_category_types'=>$pc_category_types,
            'npc_category_types'=>$npc_category_types,
            'pricelist'=>$pl,
            'warehouse'=>$wh,
            'pendingflag' => $user->can('approve_product')? 1: 0,
            'tallyflag' => $user->can('sync_tally')? 1: 0,
        ]);
    }
    public function save(Request $request)
    {
        //dd($request);
        $category_types = CategoryType::all();
        $warehouses = Warehouse::all();
        $pricelists = Pricelist::all();
        $val_array = [
            'name' => 'required|unique:products',
            'weight' => 'numeric',
            'mrp' => 'numeric',
            'landing_price' => 'numeric',
            'general_selling_price' => 'numeric',
            'general_selling_dealer' => 'numeric',
        ];
        foreach ($category_types as $item) {
            $val_array[$item->slug] = 'required';
        }
        foreach ($warehouses as $warehouse) {
            $val_array['warehouse_'.$warehouse->slug] = 'integer';
        }
        //dd($val_array);
        $validator = Validator::make($request->all(), $val_array);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $fields = helper('apex')->get_fields('product');
        $obj = new Product;
        foreach ($fields as $key => $value) {
            if(isset($value['avoid']) && $value['avoid']){
                continue;
            }
            $obj->$key = $request->$key;
        }
        $obj->name = trim($obj->name);
        $obj->slug = Str::slug($obj->name,'_');
        $obj->sku='dash'.$obj->slug;
        $obj->publish = $request->pending ? $request->pending : 0 ;
        $obj->tally = $request->tally ? $request->tally : 0 ;
        $obj->save();
        foreach ($category_types as $type) {
            $obj->categories()->attach($request->get($type->slug));
            if($type->in_pc)
            {
                $code_array[$type->id] = Category::find($request->get($type->slug))->code;
            }
        }

        $code_orders = CodeOrder::orderby('order')->get();
        $p_code = '';
        foreach($code_orders as $code_order){
            $p_code .=$code_array[$code_order->type_id];
        }
        //dd($p_code);
        $p_code = helper('apex')->make_unique_code($p_code);
        $obj->sku = $p_code;
        $obj->save();
        
        foreach ($pricelists as $pl) {
            $obj->pricelists()->attach($pl->id,[
                'value'=> $request->get($pl->slug) ? $request->get($pl->slug):0
            ]);
        }
        $warehouses = Warehouse::all();
        $arr=[];
        foreach ($warehouses as $wh) {
            $arr[$wh->id] = ['stock'=>$request->get('warehouse_'.$wh->slug) ? $request->get('warehouse_'.$wh->slug):0];
        }
        $obj->warehouses()->sync($arr);
        return response()->json([
            'message'=>'success'
        ]);
    }
    public function edit(Request $request, $id){
        $user = \Auth::user();
        $obj = Product::find($id);
        //dd($obj);
        $category_types = CategoryType::all();
        $pc_category_types = [];
        $npc_category_types = [];
        if($category_types->count() > 0)
        {
            foreach ($category_types as $category_type) {
                $options=[];
                $temp = Category::where('type_id',$category_type->id)->get();
                foreach ($temp as $it) {
                    $options[] = ['text'=>$it->name,'value'=>$it->id];
                }
                $v = $obj->categories()->get()->where('type_id',$category_type->id)->first();
                if($category_type->in_pc == 1)
                {
                    
                    $pc_category_types[$category_type->slug] = [
                        'label' => $category_type->name,
                        'options' => $options,
                        'value' => ['text'=>($v ? $v->name: ''),'value'=>($v ? $v->id: '')],
                        'error' => ''
                    ];
                }
                else
                {
                    $npc_category_types[$category_type->slug] = [
                        'label' => $category_type->name,
                        'options' => $options,
                        'value' => ($v ? $v->id: ''),
                        'error' => ''
                    ];
                }
            }
        }
        $fields = helper('apex')->get_fields('product');
        $formdata = [];
        foreach ($fields as $key => $value) {
            $formdata[$key] = ['value' => ($obj->$key !== 'null' ? strval($obj->$key) : ''),'error'=>''];
        }
        $formdata['pending'] =['value'=>$obj->publish,'error'=>''];
        $formdata['tally'] =['value'=>$obj->tally,'error'=>''];
        //dd($formdata);
        $formdata['gst']['options'] = [
            ['text' =>'12%','value'=>'12.00'],
            ['text' =>'18%','value'=>'18.00'],
            ['text' =>'5%','value'=>'5.00']
        ];
        $pricelists = Pricelist::all();
        $pl=[];
        if($pricelists->count() >0)
        {
            foreach ($pricelists as $pricelist) {
                $v = $obj->pricelists()->where('pricelist_id',$pricelist->id)->first();
                //dd($v);
                $pl[$pricelist->slug] = [
                    'id'=>$pricelist->id,
                    'name'=>$pricelist->name,
                    'value'=> $v ? strval($v->pivot->value) : strval(0),
                    'error'=> ''
                ];
            }
            
        }
        $warehouses = Warehouse::all();
        $wh = [];
        if($warehouses->count() > 0)
        {
            foreach ($warehouses as $warehouse) {
                $v = $obj->warehouses()->where('warehouse_id',$warehouse->id)->first();
                $wh['warehouse_'.$warehouse->slug] = [
                    'id'=>$warehouse->id,
                    'name'=>$warehouse->name,
                    'value'=> $v ? strval($v->pivot->stock) : strval(0),
                    'error'=> '',
                ];
            }
        }
        return response()->json([
            'formdata' => $formdata,
            'pc_category_types'=>$pc_category_types,
            'npc_category_types'=>$npc_category_types,
            'pricelist'=>$pl,
            'warehouse' => $wh,
            'pendingflag' => $user->can('approve_product')? '1': '0',
            'tallyflag' => $user->can('sync_tally')? '1': '0',
        ]);
    }
    public function update(Request $request,$id)
    {
        //dd($request->toArray());
        $category_types = CategoryType::all();
        $warehouses = Warehouse::all();
        $val_array = [
            'name' => [
                'required',
                Rule::unique('products')->ignore($id)
            ],
            'weight' => 'numeric',
            'mrp' => 'numeric',
            'landing_price' => 'numeric',
            'general_selling_price' => 'numeric',
            'general_selling_dealer' => 'numeric',
        ];
        foreach ($category_types as $item) {
            $val_array[$item->slug] = 'required';
        }
        foreach ($warehouses as $warehouse) {
            $val_array['warehouse_'.$warehouse->slug] = 'integer';
        }
        //dd($val_array);
        $validator = Validator::make($request->all(), $val_array);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $fields = helper('apex')->get_fields('product');
        $obj = Product::find($id);
        foreach ($fields as $key => $value) {
            if(isset($value['avoid']) && $value['avoid']){
                continue;
            }
            $obj->$key = $request->$key;
        }
        $obj->name = trim($obj->name);
        $obj->slug = Str::slug($obj->name,'_');
        $obj->sku='dash'.$obj->slug;
        $obj->publish = $request->pending !== '' ? $request->pending : $obj->publish ;
        $obj->tally = $request->tally !== '' ? $request->tally : $obj->tally ;
        $obj->save();
        $sync_array = [];
        foreach ($category_types as $type) {
            $sync_array[] = $request->get($type->slug);
            if($type->in_pc)
            {
                $code_array[$type->id] = Category::find($request->get($type->slug))->code;
            }
        }
        //dd($sync_array);
        $obj->categories()->sync($sync_array);
        $code_orders = CodeOrder::orderby('order')->get();
        $p_code = '';
        foreach($code_orders as $code_order){
            $p_code .=$code_array[$code_order->type_id];
        }
        $p_code = helper('apex')->make_unique_code($p_code,$obj->id);
        $obj->sku = $p_code;
        $obj->save();
        $pricelists = Pricelist::all();
        $arr=[];
        foreach ($pricelists as $pl) {
            $arr[$pl->id] = ['value'=>$request->get($pl->slug) ? $request->get($pl->slug):0];
        }
        $obj->pricelists()->sync($arr);

        $warehouses = Warehouse::all();
        $arr=[];
        foreach ($warehouses as $wh) {
            $arr[$wh->id] = ['stock'=>$request->get('warehouse_'.$wh->slug) ? $request->get('warehouse_'.$wh->slug):0];
        }
        $obj->warehouses()->sync($arr);
        return response()->json([
            'message'=>'success'
        ]);
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
    public function export()
    {
        return (new ProductsExport)->download('products.xlsx');
    }
    public function import(Request $request)
    {
        //dd($request->toArray());
        $file = $request->file('file');
        $method = $request->method;
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
                $import = new ProductsImport($method);
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
    public function settings(){
        $panels=[];
        $panels=Eventy::filter('products.settings.panel',$panels);
        return response()->json(['panels'=>$panels]);
    }
    public function list_settings(Request $request)
    {
        $user=Auth::user();
        ProductListSettings::updateOrCreate(['user_id'=>$user->id],['value'=>$request->get('fields')]);
        return response()->json(['message'=>'success']);
    }
    public function get_filterables()
    {
        $filterables = helper('apex')->get_filterables('product',Product::class);
        return response()->json(['filterables'=>$filterables]);
    }
    public function sample_event(Request $request){
        $msg = $request->msg;
        event(new SampleEvent($msg));
    }
    public function pricelist()
    {
        $objects = Pricelist::all();
        $items = [];
        foreach ($objects as $obj) {
            $items[] = [
                'id' => $obj->id,
                'name'=>$obj->name,
                'actions'=>[
                    'actions'=>true,
                    'edit'=>true,
                    'delete'=>true,
                    'id' => $obj->id,
                    'delete_route' => '/products/pricelist/delete',
                ],
            ];
        }
        $headers = [
            [
                'text' => 'Name','value' => 'name'
            ],
            [
                'text'=>'Actions','value'=>'actions','align'=>'right',
            ]
        ];
        return response()->json([
            'list' => [
                'headers' => $headers,
                'items' => $items,
            ],
        ]);
    }
    public function save_pricelist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:pricelist',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $obj = new Pricelist;
        $obj->name = $request->name;
        $obj->slug = Str::slug($request->name,'_');
        $obj->save();
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function edit_pricelist(Request $request, $id)
    {
        $obj = Pricelist::find($id);
        return response()->json([
            'formdata' => [
                'name' => [
                    'value'=>$obj->name,
                    'error' => '',
                ]
            ]
        ]);
    }
    public function update_pricelist(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('pricelist')->ignore($id),
            ]
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $obj = Pricelist::find($id);
        $obj->name = $request->name;
        $obj->slug = Str::slug($request->name,'_');
        $obj->save();
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function delete_pricelist(Request $request)
    {
        $id = $request->id;
        Pricelist::destroy($id);
    }
    public function tally_sync(Request $request)
    {
        $ids = explode(",",$request->ids);
        foreach ($ids as $id) {
            $obj =  Product::find($id);
            $obj->tally = 1;
            $obj->save();
        }
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function temp()
    {
        $objs = Product::all();
        $warehouses = Warehouse::all();
        foreach ($objs as $obj) {
            $total = 0;
            foreach ($warehouses as $warehouse) {
                $v = $obj->warehouses()->where('warehouse_id',$warehouse->id)->first();
                $total = $total + $v->pivot->stock;
            }
            $obj->stock = $total;
            $obj->save();
        }
    }
}
