<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Customer\Models\Customer;
use App\Modules\Customer\Models\Address;
use App\Modules\Customer\Models\Phone;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use TorMorten\Eventy\Facades\Events as Eventy;
class CustomerController extends Controller
{
	protected $searcheable =['name'];
	public function getMenu()
    {
        $user = \Auth::user();
        $items = [];
        if($user->can('list_customers')){
            $items[] = [
                'text'=>'Customers',
                'route'=>'/customers/list',
                'icon'=>'list_alt',
            ];
        }
        return response()->json([
            'items' => $items
        ]);
    }
	public function index(Request $request)
    {
        $user=\Auth::user();
        $headers=[];
        $select_array = ['id','name','email'];
        $list_terms = helper('apex')->get_list_terms($request,'customer',$select_array,$this->searcheable);
        if($request->get('search')){
            $data = Customer::where($list_terms['search'])->get();
        }
        else{
            $data = Customer::all();
        }
        $list = helper('apex')->perform_filtering($data,$list_terms['rpp'],$list_terms['page'],$select_array,$request,'customer',Customer::class);
        return response()->json(
            [
                'items'=>$list['items'],
                'headers'=>$list_terms['headers'],
                'total'=>$list['total'],
                'fields'=>'',
                'filterables'=>'',
                'filtered' => $list['filtered'],
                'addflag' => $user->can('list_customers') ? 1:0, 
                'exportflag' => 1,
                'importflag' => $user->can('create_customer') && $user->can('edit_customer') ? 1: 0,
            ]
        );
    }
    public function save(Request $request)
    {
    	$arr = json_decode($request->data,true);
    	$validator = Validator::make($arr, [
    		'name.value'=>'required|unique:customers,name',
    		'email.value'=>'required|email|unique:customers,email',
    		'phones.*.value'=>'nullable|integer',
    		'addresses.*.tag.value'=>'required',
    	]);
    	if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'errors'=>$validator->errors(),
            ]);
        }
        $obj = new Customer;
        $obj->name = $arr['name']['value'];
        $obj->slug = Str::slug($arr['name']['value'],'_');
        $obj->email = $arr['email']['value'];
        $obj->save();
        $phone_array=[];
        foreach ($arr['phones'] as $phone) {
        	$phone_array[]=['phone'=>$phone['value']];
        }
        if($phone_array)
        {
        	$obj->phones()->createMany($phone_array);
        }
        $address_array=[];
        foreach ($arr['addresses'] as $address) {
        	$address_array[] = [
        		'name'=>$address['tag']['value'],
        		'slug'=>Str::slug($address['tag']['value'],'_'),
        		'line1'=>$address['line1']['value'],
        		'line2'=>$address['line2']['value'],
        		'pin'=>$address['pin']['value'],
        		'state'=>$address['state']['value'],
        		'country'=>$address['country']['value'],
        		'tel'=>$address['tel']['value'],
        	];
        }
        if($address_array)
        {
        	$obj->addresses()->createMany($address_array);
        }
        return response()->json([
            'message'=> 'success'
        ]);
    }
    public function edit(Request $request,$id)
    {
    	$obj = Customer::find($id);
    	$formdata = [
    		'name' => [
    			'value'=>$obj->name,
    			'error'=>''
    		],
    		'email' => [
    			'value'=>$obj->email,
    			'error'=>''
    		]
    	];
    	$phones = $obj->phones()->get();
    	$ph = [];
    	foreach ($phones as $phone) {
    		$ph[] = [
    			'value'=>$phone->phone,
    			'error'=>''
    		];
    	}
    	$formdata['phones'] = $ph;
    	$addresses = $obj->addresses()->get();
    	$ad = [];
    	foreach ($addresses as $address) {
    		$ad[] = [
    			'tag' => ['value'=>$address->name,'error'=>''],
    			'line1' => ['value'=>$address->line1,'error'=>''],
    			'line2' => ['value'=>$address->line2,'error'=>''],
    			'pin' => ['value'=>$address->pin,'error'=>''],
    			'state' => ['value'=>$address->state,'error'=>''],
    			'country' => ['value'=>$address->country,'error'=>''],
    			'tel' => ['value'=>$address->tel,'error'=>''],
    		];
    	}
    	$formdata['addresses'] = $ad;

    	return response()->json([
    		'formdata' => $formdata
    	]);
    }
    public function update(Request $request,$id)
    {
    	$obj = Customer::find($id);
    	$arr = json_decode($request->data,true);
    	$validator = Validator::make($arr, [
    		'name.value'=>'required|unique:customers,name,'.$id,
    		'email.value'=>'required|email|unique:customers,email,'.$id,
    		'phones.*.value'=>'nullable|integer',
    		'addresses.*.tag.value'=>'required',
    	]);
    	$obj->name = $arr['name']['value'];
        $obj->slug = Str::slug($arr['name']['value'],'_');
        $obj->email = $arr['email']['value'];
        $obj->save();
        $obj->phones()->delete();
    	$obj->addresses()->delete();
        $phone_array=[];
        foreach ($arr['phones'] as $phone) {
        	$phone_array[]=['phone'=>$phone['value']];
        }
        if($phone_array)
        {
        	$obj->phones()->createMany($phone_array);
        }
        $address_array=[];
        foreach ($arr['addresses'] as $address) {
        	$address_array[] = [
        		'name'=>$address['tag']['value'],
        		'slug'=>Str::slug($address['tag']['value'],'_'),
        		'line1'=>$address['line1']['value'],
        		'line2'=>$address['line2']['value'],
        		'pin'=>$address['pin']['value'],
        		'state'=>$address['state']['value'],
        		'country'=>$address['country']['value'],
        		'tel'=>$address['tel']['value'],
        	];
        }
        if($address_array)
        {
        	$obj->addresses()->createMany($address_array);
        }
        return response()->json([
            'message'=> 'success'
        ]);
    }
    public function delete(Request $request)
    {
        $ids = explode(",",$request->delete_ids);
        foreach ($ids as $id) {
        	$obj = Customer::find($id);
        	$obj->phones()->delete();
        	$obj->addresses()->delete();
        }
        Customer::destroy($ids);
        return response()->json([
            'message'=>'success',
        ]);
    }
}