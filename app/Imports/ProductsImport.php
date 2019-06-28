<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use App\Modules\Product\Models\Product;
use App\Modules\Taxonomy\Models\Category;
use App\Modules\Taxonomy\Models\CategoryType;
use App\Modules\ProductCode\Models\CodeOrder;
use App\Modules\Product\Models\Pricelist;
use App\Modules\Product\Models\Warehouse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
class ProductsImport implements ToCollection, WithHeadingRow 
{
	use Importable;
    /**
    * @param Collection $collection
    */
    protected $method ='';

    public function __construct($method)
    {
    	$this->method = $method;
    }
    public function collection(Collection $rows)
    {
    	if($this->method == 'create')
    	{
    		$category_types = CategoryType::all();
    		$warehouses = Warehouse::all();
    		$pricelists = Pricelist::all();
    		$cat_val =[];
    		foreach ($category_types as $type) {
	        	$arr = Category::select('name')->where('type_id',$type->id)->get();
	        	foreach ($arr as $item) {
	        		$cat_val[$type->id][]=$item->name;
	        	}
	        }
	        $val_array = [
	            '*.name' => 'required|unique:products',
	            '*.weight' => 'numeric',
	            '*.stock' => 'integer',
	            '*.mrp' => 'numeric',
	            '*.landing_price' => 'numeric',
	            '*.general_selling_price' => 'numeric',
	            '*.general_selling_dealer' => 'numeric',
	            '*.gst' => [
	            	'required',
	            	Rule::in([5,12,18]),
	            ],
	        ];

	        foreach ($category_types as $type) {
	            $val_array['*.'.$type->slug] = [
	            	'required',
	            	Rule::in($cat_val[$type->id]),
	            ];
	        }
	        foreach ($warehouses as $warehouse) {
	            $val_array['warehouse_'.$warehouse->slug] = 'integer';
	        }
	        $rows = $rows->toArray();
	        $validator = Validator::make($rows, $val_array, [], []);
	        if($validator->fails()){
	        	$e1 = new IlluminateValidationException($validator->errors());
	        	$e = $validator;
		        $failures = [];
		        foreach ($e->errors()->toArray() as $attribute => $messages) {
		            $row           = strtok($attribute, '.');
		            $attributeName = strtok('');
		            $attributeName = $attributes['*.' . $attributeName] ?? $attributeName;
		            $failures[] = new Failure(
		                $row,
		                $attributeName,
		                str_replace($attribute, $attributeName, $messages),
		                $rows[$row]
		            );
		        }
		        throw new \Maatwebsite\Excel\Validators\ValidationException(
		            $e1,
		            $failures
		        );
		    }
	    	foreach ($rows as $row) {

	    		$fields = helper('apex')->get_fields('product');
		        $obj = new Product;
		        foreach ($fields as $key => $value) {
		        	if(isset($row[$key])){
		        		$obj->$key = $row[$key];
		        	}
		        }
		        $obj->name = trim($obj->name);
		        $obj->slug = Str::slug($obj->name,'_');
		        $obj->sku='dash'.$obj->slug;
		        $obj->publish = $row['publish'] ? $row['publish'] : 0 ;
	        	$obj->tally = $row['tally'] ? $row['tally'] : 0 ;
		        $obj->save();
		        $code_array = [];
		        foreach ($category_types as $type) {
		        	$cat_id = Category::where([
		        		['name',$row[$type->slug]],
		        		['type_id',$type->id]
		        	])->first()->id;
		            $obj->categories()->attach($cat_id);
		            if($type->in_pc)
		            {
		                $code_array[$type->id] = Category::find($cat_id)->code;
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
		                'value'=> $row['pricelist_margin_'.$pl->slug] ? $row['pricelist_margin_'.$pl->slug]: 0
		            ]);
		        }
		        $arr=[];
		        foreach ($warehouses as $wh) {
		            $arr[$wh->id] = ['stock'=>$row['warehouse_'.$wh->slug] ? $row['warehouse_'.$wh->slug]:0];
		        }
		        $obj->warehouses()->sync($arr);
	    	}
    	}
    	if($this->method == 'update')
    	{
    		$category_types = CategoryType::all();
    		$warehouses = Warehouse::all();
    		$pricelists = Pricelist::all();
    		$cat_val =[];
    		foreach ($category_types as $type) {
	        	$arr = Category::select('name')->where('type_id',$type->id)->get();
	        	foreach ($arr as $item) {
	        		$cat_val[$type->id][]=$item->name;
	        	}
	        }
	        $rows = $rows->toArray();
	        $count=1;
	    	foreach ($rows as $row) {
	    		$fields = helper('apex')->get_fields('product');
		        $obj = Product::find($row['id']);
		        $val_array = [
		        	'id' => 'required',
		            'name' => [
		            	'required',
		            	Rule::unique('products')->ignore($row['id']),
		            ],
		            'weight' => 'numeric',
		            'stock' => 'integer',
		            'mrp' => 'numeric',
		            'landing_price' => 'numeric',
		            'general_selling_price' => 'numeric',
		            'general_selling_dealer' => 'numeric',
		            'gst' => [
		            	'required',
		            	Rule::in([5,12,18]),
		            ],
		        ];

		        foreach ($category_types as $type) {
		            $val_array[$type->slug] = [
		            	'required',
		            	Rule::in($cat_val[$type->id]),
		            ];
		        }
		        foreach ($warehouses as $warehouse) {
		            $val_array['warehouse_'.$warehouse->slug] = 'integer';
		        }
		        $validator = Validator::make($row, $val_array, [], []);
		        if($validator->fails()){
		        	$e1 = new IlluminateValidationException($validator->errors());
		        	$e = $validator;
			        $failures = [];
			        foreach ($e->errors()->toArray() as $attribute => $messages) {
			            $failures[] = new Failure(
			                $count,
			                $attribute,
			                $messages,
			                $row
			            );
			        }
			        throw new \Maatwebsite\Excel\Validators\ValidationException(
			            $e1,
			            $failures
			        );
			    }
		        foreach ($fields as $key => $value) {
		        	if(isset($row[$key])){
		        		$obj->$key = $row[$key];
		        	}
		        }
		        $obj->name = trim($obj->name);
		        $obj->slug = Str::slug($obj->name,'_');
		        $obj->sku='dash'.$obj->slug;
		        $obj->publish = $row['publish'] ? $row['publish'] : 0 ;
	        	$obj->tally = $row['tally'] ? $row['tally'] : 0 ;
		        $obj->save();
		        $code_array = [];
		        $sync_array = [];
		        foreach ($category_types as $type) {

		        	$cat_id = Category::where([
		        		['name',$row[$type->slug]],
		        		['type_id',$type->id]
		        	])->first()->id;
		        	$sync_array[] = $cat_id;
		            if($type->in_pc)
		            {
		                $code_array[$type->id] = Category::find($cat_id)->code;
		            }
		        }

		        $obj->categories()->sync($sync_array);
		        $code_orders = CodeOrder::orderby('order')->get();
		        $p_code = '';
		        foreach($code_orders as $code_order){
		            $p_code .=$code_array[$code_order->type_id];
		        }
		        //dd($p_code);
		        $p_code = helper('apex')->make_unique_code($p_code,$obj->id);
		        $obj->sku = $p_code;
		        $obj->save();
		        
		        foreach ($pricelists as $pl) {
		        	$p_sync_array[$pl->id] = ['value'=>$row['pricelist_margin_'.$pl->slug] ? $row['pricelist_margin_'.$pl->slug]:0];
		        }
		        $obj->pricelists()->sync($p_sync_array);
		        $arr=[];
		        foreach ($warehouses as $wh) {
		            $arr[$wh->id] = ['stock'=>$row['warehouse_'.$wh->slug] ? $row['warehouse_'.$wh->slug]:0];
		        }
		        $obj->warehouses()->sync($arr);
		        $count++;
	    	}
	    	
    	}
    }
}
