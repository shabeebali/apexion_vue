<?php
namespace App\Modules\Sale\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    public function items()
    {
    	return $this->hasMany('App\Modules\Sale\Models\OrderItems');
    }
}
