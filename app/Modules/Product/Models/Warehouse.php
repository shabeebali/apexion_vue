<?php
namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'warehouses';

    public function areas()
    {
    	return $this->hasMany('App\Modules\Product\Models\WArea');
    }
    public function racks()
    {
    	return $this->hasMany('App\Modules\Product\Models\WRack');
    }
    public function slots()
    {
    	return $this->hasMany('App\Modules\Product\Models\WSlot');
    }
    public function products(){
    	return $this->belongsToMany('App\Modules\Product\Models\Product','product_warehouse','warehouse_id','product_id')->withPivot('stock');;
    }
}
