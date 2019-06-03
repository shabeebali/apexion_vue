<?php
namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public function categories()
    {
        return $this->belongsToMany('App\Modules\Product\Models\Category','category_product','product_id','category_id');
    }
}
