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
    public function customer()
    {
    	return $this->belongsTo('App\Modules\Customer\Models\Customer','customer_id');
    }
    public function user()
    {
    	return $this->belongsTo('App\User','user_id');
    }
}
