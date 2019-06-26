<?php
namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public function categories()
    {
        return $this->belongsToMany('App\Modules\Taxonomy\Models\Category','category_product','product_id','category_id');
    }
    public function pricelists()
    {
    	return $this->belongsToMany('App\Modules\Product\Models\Pricelist','pricelist_product','product_id','pricelist_id')->withPivot('value');
    }
    public function warehouses(){
    	return $this->belongsToMany('App\Modules\Product\Models\Warehouse','product_warehouse','product_id','warehouse_id')->withPivot('stock');;
    }
}
