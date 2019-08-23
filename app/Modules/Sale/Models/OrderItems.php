<?php
namespace App\Modules\Sale\Models;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    protected $table = 'order_items';
    public $timestamps = false;
}
