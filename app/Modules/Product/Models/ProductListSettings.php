<?php
namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;

class ProductListSettings extends Model
{
    protected $table = 'product_list_settings';
    protected $fillable = ['user_id','value'];
}
