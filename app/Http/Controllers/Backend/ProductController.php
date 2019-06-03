<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Taxonomy\Models\Category;
use App\Modules\Taxonomy\Models\CategoryType;
use App\Modules\Product\Models\ProductListSettings;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Str;
use App\Modules\Product\Events\ProductAddFormGenerated;
use TorMorten\Eventy\Facades\Events as Eventy;
class ProductController extends Controller
{
    protected $searcheable =['name'];

    public function index(Request $request)
    {

        $user=Auth::user();
        $headers=[];
        $select_array = ['id','name','mrp','general_selling_price'];
        $list_settings = ProductListSettings::select('value')->where('user_id',$user->id)->first();
        if($list_settings)
        {
            $select_array = explode(",",$list_settings->value);
            array_push($select_array,'id');
        }
        $list_terms = helper('apex')->get_list_terms($request,'product',$select_array,$this->searcheable);
        if($request->get('search')){
            $data = Product::where($list_terms['search'])->get();
        }
        else{
            $data = Product::all();
        }
        $list = helper('apex')->perform_filtering($data,$list_terms['rpp'],$list_terms['page'],$select_array,$request,'product',Product::class);
        return response()->json(
            [
                'items'=>$list['items'],
                'headers'=>$list_terms['headers'],
                'total'=>$list['total'],
                'fields'=>$list_terms['fields'],
                'filterables'=>$list['filterables'],
                'filtered' => $list['filtered'],
            ]
        );
    }
    public function add(){
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
        return response()->json([
            'pc_category_types'=>$pc_category_types,
            'npc_category_types'=>$npc_category_types,
            'pricelist'=>''
        ]);
    }
    public function save(Request $request)
    {
        //dd($request);
        $pc_category_types = CategoryType::where('in_pc',1)->get();
        $val_array = [
            'name' => 'required|unique:products',
            'weight' => 'numeric',
            'stock' => 'integer',
            'mrp' => 'numeric',
            'landing_price' => 'numeric',
            'general_selling_price' => 'numeric',
            'general_selling_dealer' => 'numeric',
        ];
        foreach ($pc_category_types as $item) {
            $val_array[$item->slug] = 'required';
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
            $obj->$key = $request->$key;
        }
        $obj->name = trim($obj->name);
        $obj->slug = Str::slug($obj->name);
        $obj->sku='dash'.$obj->slug;
        $obj->save();
        foreach ($pc_category_types as $type) {
            $obj->categories()->attach($request->get($type->slug));
        }
        return response()->json([
            'message'=>'success'
        ]);
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
}
