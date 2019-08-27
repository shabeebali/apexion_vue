<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
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
		$objs = Order::select('id','ref','status','created_at','customer_id','user_id');
		if($request->search)
		{
			$objs = $objs->whereHas('customer', function ($query) use ($request){
			    $query->where('name', 'like', '%'.$request->search.'%');
			});
		}
		if($request->sortby){
			$objs = $objs->orderBy($request->sortby,($request->descending ? 'desc':'asc'));
		}
		else{
			$objs = $objs->orderBy('id','desc');
		}
		$objs = $objs->get();	
		
		//dd($objs->toArray());
		$headers = [
			[
				'text'=>'Ref',
				'value' => 'ref'
			],
			[
				'text'=>'Customer',
				'value'=>'customer',
				'sortable'=>false
			],
			[
				'text'=>'Status',
				'value'=>'status'
			],
			[
				'text'=>'Created By',
				'value'=>'created_by',
				'sortable'=>false
			],
			[
				'text'=>'Date',
				'value'=>'created_at'
			],
			[
				'text'=>'Actions',
				'value'=>'actions',
				'sortable'=>false
			]
		];
		$items = [];
		foreach ($objs as $obj) {
			$date = new Carbon($obj->created_at);
			$items[] = [
				'ref'=>$obj->ref,
				'customer'=>($obj->customer_id == 0 ? '' : $obj->customer->name),
				'status'=>$obj->status,
				'created_by'=>$obj->user->name,
				'created_at'=>$date->toFormattedDateString(),
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
	public function users()
	{
		$objs = \App\User::all();
		$current_user = \Auth::user();
		$users = [];
		foreach ($objs as $obj) {
			if($obj->hasRole('sale'))
			{
				$users[] = [
					'text' => $obj->name,
					'value' => $obj->id,
					'default' => $obj->id == $current_user->id ? 1: 0
				];
			}
		}
		return response()->json(
            [
                'users'=>$users,
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
		//dd($request->draft_id);

		$user = \Auth::user();
		if($request->draft_id != '0')
		{
			$order = Order::find($request->draft_id);
		}
		else{
			$order = new Order;
		}
		//dd($order);
		$order->user_id = $request->created_by;
		$order->status = $request->status;
		$order->pricelist_id = $request->pricelist_id;
		$order->gst_included = $request->gst_included;
		$order->salesperson_id = $request->salesperson;
		$order->freight = $request->freight;
		$order->customer_id = ($request->customer_id == 'undefined' ? 0:$request->customer_id) ;
		$order->discount = $request->discount;
		$order->total = $request->total;
		$order->tax = $request->tax;
		$count = 0;
		$ref_num = Carbon::now();
		$ref_num = $ref_num->format('ymd');
		$ref_num = $ref_num.str_pad($count, 3,0,STR_PAD_LEFT);
		
		$data['ref_num']= $ref_num;
		if($request->draft_id != '0')
		{
			
	        while(Validator::make($data,['ref_num'=>'unique:orders,ref,'.$request->draft_id])->fails()){
	            $ref_num = substr($ref_num, 0, -3);
	            $count = $count + 1;
	            $ref_num = $ref_num.str_pad($count, 3,0,STR_PAD_LEFT);
	            $data['ref_num']= $ref_num;
	        }
		}
		else
		{
			while(Validator::make($data,['ref_num'=>'unique:orders,ref'])->fails()){
	            $ref_num = substr($ref_num, 0, -3);
	            $count = $count + 1;
	            $ref_num = $ref_num.str_pad($count, 3,0,STR_PAD_LEFT);
	            $data['ref_num']= $ref_num;
	        }
	    }
        $order->ref = $ref_num;
		$order->save();
		if($request->draft_id != 0)
		{
			$items = $order->items()->get();
			foreach ($items as $item) {
				OrderItems::destroy($item->id);
			}
		}
		$items = json_decode($request->items,true);
		foreach ($items as $item) {
			$order_items = new OrderItems;
			$order_items->product_id = $item['id'];
			$order_items->price = $item['price'];
			$order_items->rate = $item['rate'];
			$order_items->quantity = $item['qty'];
			$order_items->order_id = $order->id;
			$order_items->gst = $item['gst'];
			$order_items->save();
		}
		return response()->json(
            [
                'message' => 'success',
                'draft_id'=> $order->id,
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
				'gst' => $order_item->gst,
				'rate' => $order_item->rate,
				'price' => $order_item->price,
			];
			$count = $count+1;
		}
		return response()->json(
            [
            	'status'=> $order->status,
            	'id' => $order->id,
            	'freight' => $order->freight,
            	'created_by' => \App\User::find($order->user_id)->name,
            	'salesperson' => \App\User::find($order->salesperson_id)->name,
            	'ref'=>$order->ref,
                'items'=>$items,
                'customer'=> ($order->customer_id == 0 ? '': Customer::find($order->customer_id)->name),
                'discount'=>$order->discount,
                'tax' => $order->tax,
                'gst_included' => $order->gst_included,
                'total' => $order->total,
                'pricelist' => Pricelist::find($order->pricelist_id)->name
            ]
        );
	}
	public function edit(Request $request, $id)
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
				'id' => $order_item->product_id,
				'qty' => $order_item->quantity,
				'gst' => $order_item->gst,
				'rate' => $order_item->rate,
				'price' => $order_item->price,
			];
			$count = $count+1;
		}
		return response()->json(
            [
            	'status'=> $order->status,
            	'draft_id' => $order->id,
            	'user_id' => $order->user_id,
            	'salesperson_id' => $order->salesperson_id,
            	'freight' => $order->freight,
                'items'=>$items,
                'customer'=>($order->customer_id == 0 ? '': Customer::find($order->customer_id)->name),
                'customer_id'=> $order->customer_id,
                'pricelist_id'=> $order->pricelist_id,
                'discount'=>$order->discount,
                'gst_included' => $order->gst_included,
            ]
        );
	}
}