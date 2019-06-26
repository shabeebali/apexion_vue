<?php
namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;

class WArea extends Model
{
    protected $table = 'wareas';

    public function warehouse()
    {
        return $this->belongsTo('App\Modules\Product\Models\Warehouse');
    }
    public function racks()
    {
    	return $this->hasMany('App\Modules\Products\Models\WRack');
    }
    public function slots()
    {
    	return $this->hasMany('App\Modules\Products\Models\WSlot');
    }
    public function products()
    {
        return $this->belongsToMany('App\Modules\Product\Models\Product');
    }
}
