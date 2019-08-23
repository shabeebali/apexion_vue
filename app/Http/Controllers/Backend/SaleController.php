<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Pricelist;
use App\Modules\Sale\Models\Order;
use App\Modules\Customer\Models\Customer;
use App\Modules\Sale\Models\OrderItems;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
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
	public function index(Request $request)
	{
		$user = \Auth::user();
		$objs = Order::orderBy('id','desc');
		if($request->search)
		{
		}
		$objs=$objs->get();
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
				'text'=>'Status',
				'value'=>'status'
			],
			[
				'text'=>'Created By',
				'value'=>'created_by'
			],
			[
				'text'=>'Date',
				'value'=>'date'
			],
			[
				'text'=>'Actions',
				'value'=>'actions'
			]
		];
		$items = [];
		foreach ($objs as $obj) {
			$date = new Carbon($obj->created_at);
			$items[] = [
				'order_id'=>$obj->id,
				'customer'=>$obj->customer->name,
				'status'=>$obj->status,
				'created_by'=>$obj->user->name,
				'date'=>$date->toFormattedDateString(),
				'actions' =>[
					'actions'=>1,
                    'view'=>1,
                    'id'=>$obj->id,
				]
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
	public function getPl()
	{
		$objs = Pricelist::all();
		$items=[];
		foreach ($objs as $obj) {
			$items[] = [
				'text' => $obj->name,
				'value' => $obj->id,
			];
		}
		return response()->json(
            [
                'items'=>$items,
            ]
        );
	}
	public function getRate(Request $request)
	{
		$obj = Product::find($request->id);
		$v = $obj->pricelists()->where('pricelist_id',$request->pl)->first();
		return response()->json(
            [
                'gst'=>$obj->gst,
                'landing_price' => $obj->landing_price,
                'value' => $v ? strval($v->pivot->value) : strval(0),
            ]
        );
	}
	public function save(Request $request)
	{

		$user = \Auth::user();
		$order = new Order;
		$order->user_id = $user->id;
		$order->status = 'processing';
		$order->customer_id = $request->customer_id;
		$order->discount = $request->discount;
		$order->total = $request->total;
		$order->save();
		$items = json_decode($request->items,true);
		foreach ($items as $item) {
			$order_items = new OrderItems;
			$order_items->product_id = $item['id'];
			$order_items->price = $item['price'];
			$order_items->rate = $item['rate'];
			$order_items->quantity = $item['qty'];
			$order_items->order_id = $order->id;
			$order_items->save();
		}
		return response()->json(
            [
                'message' => 'success'
            ]
        );
	}
	public function complete(Request $request)
	{
		$id = $request->id;
		$order = Order::find($id);
		$order->status = 'completed';
		$order->save();
		return response()->json(
            [
                'message' => 'success'
            ]
        );
	}
	public function view(Request $request, $id)
	{
		$order = Order::find($id);
		$order_items = $order->items()->get();
		$count = 1;
		$items = [];
		foreach($order_items as $order_item)
		{
			$items[] =[
				'line' => $count,
				'product' => Product::find($order_item->product_id)->name,
				'qty' => $order_item->quantity,
				'rate' => $order_item->rate,
				'price' => $order_item->price,
			];
			$count = $count+1;
		}
		return response()->json(
            [
            	'order_id'=>$order->id,
                'items'=>$items,
                'customer'=>Customer::find($order->customer_id)->name,
                'discount'=>$order->discount,
                'total' => $order->total,
            ]
        );
	}
}