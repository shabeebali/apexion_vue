<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Pricelist;
use App\Modules\Product\Models\Warehouse;
use App\Modules\Taxonomy\Models\CategoryType;

class ProductsExport implements FromCollection, WithHeadings
{
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data = '';
    public function collection()
    {
    	$category_types = CategoryType::all();
        $pricelists = Pricelist::all();
        $warehouses = Warehouse::all();
        $data = Product::all();
        $data = $data->map(function($item,$key) use ($category_types, $pricelists,$warehouses){
        	foreach ($category_types as $category_type) {
        		$item[$category_type['slug']] =  $item->categories()->where('type_id',$category_type->id)->first()->name;
        	}
            foreach ($pricelists as $pricelist) {
                $v = $item->pricelists()->where('pricelist_id',$pricelist->id)->first();
                $item['pricelist_margin_'.$pricelist->slug] = $v ? $v->pivot->value : strval(0);
            }
            foreach ($warehouses as $warehouse) {
                $v = $item->warehouses()->where('warehouse_id',$warehouse->id)->first();
                $item['warehouse_'.$warehouse->slug] = $v ? $v->pivot->stock: strval(0);
            }
        	return $item;
        });
        $this->data = $data;
        return $data;
    }
    public function headings(): array
    {
    	$category_types = CategoryType::all();
        $pricelists = Pricelist::all();
        $warehouses = Warehouse::all();
        $data = Product::first();
    	foreach ($category_types as $category_type) {
    		$data[$category_type['slug']] =  0;
    	}
        foreach ($pricelists as $pricelist) {
            $data['pricelist_margin_'.$pricelist->slug] = 0;
        }
        foreach ($warehouses as $warehouse) {
            $data['warehouse_'.$warehouse->slug] = 0;
        }
        $arr = $data->toArray();
        return array_keys($arr);
    }
}
