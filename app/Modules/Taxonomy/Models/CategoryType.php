<?php
namespace App\Modules\Taxonomy\Models;
use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    protected $table = 'category_types';

    public function categories()
    {
        return $this->hasMany('App\Modules\Taxonomy\Models\Category','type_id');
    }
}
