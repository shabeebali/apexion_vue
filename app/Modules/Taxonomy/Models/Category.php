<?php
namespace App\Modules\Taxonomy\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    public function type()
    {
        return $this->belongsTo('App\Modules\Taxonomy\Models\CategoryType');
    }
    public function products()
    {
        return $this->belongsToMany('App\Modules\Product\Models\Product','category_product','category_id','product_id');
    }
}
