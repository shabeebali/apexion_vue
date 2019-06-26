<?php
namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;

class WRack extends Model
{
    protected $table = 'wslots';

    public function warehouse()
    {
        return $this->belongsTo('App\Modules\Product\Models\Warehouse');
    }
    public function area()
    {
    	return $this->belongsTo('App\Modules\Products\Models\WArea');
    }
    public function rack()
    {
        return $this->belongsTo('App\Modules\Products\Models\WRack');
    }
    public function products()
    {
        return $this->belongsToMany('App\Modules\Product\Models\Product');
    }
}
