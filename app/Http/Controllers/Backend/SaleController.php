<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Sale\Models\Order;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;;
class SaleController extends Controller
{
	public function getMenu()
    {
        $user = \Auth::user();
        $items = [];
        $items[]=[
            'menu_name' => 'Sale'
        ];
        if($user->can('list_orders')){
            $items[] = [
                'text'=>'Orders',
                'route'=>'/sale/orders',
                'icon'=>'list_alt',
            ];
        }

        return response()->json([
            'items' => $items
        ]);
    }
	public function index()
	{
		$user = \Auth::user();
		$objs = Order::all();
		$headers = [
			[
				'text'=>'Order ID',
				'value' => 'order_id'
			],
			[
				'text'=>'Customer',
				'value'=>'customer'
			],
			[
				'text'=>'Created By',
				'value'=>'created_by'
			],
			[
				'text'=>'Date',
				'value'=>'date'
			]
		];
		$items = [];
		foreach ($objs as $obj) {
			$items[] = [
				'order_id'=>$obj->id,
				'customer'=>$obj->customer()->name,
				'created_by'=>$obj->user()->name,
				'date'=>$obj->created_at,
			];
		}
		return response()->json(
            [
                'items'=>$items,
                'headers'=>$headers,
                'total'=>$objs->count(),
                'fields'=>'',
                'filterables'=>'',
                'filtered' => '',
                'addflag' => $user->can('create_order') ? 1:0, 
                'exportflag' => 0,
                'importflag' => 0,
            ]
        );
	}
	public function save()
	{

	}
}