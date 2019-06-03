<?php
namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $table = 'categories';

    public function products()
    {
        return $this->belongsToMany('App\Modules\Product\Models\Product','category_product','category_id','product_id');
    }
}
