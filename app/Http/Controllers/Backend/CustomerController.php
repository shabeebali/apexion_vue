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
use App\Imports\CustomerImport;
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
        $data = Customer::select($select_array);
        if($request->pending)
        {
            $data = $data->where('publish',0);
        }
        elseif($request->tally)
        {
            $data = $data->where('tally',0)->where('publish',1);
        }
        else
        {
            $data = $data->where('publish',1);
        }
        if($request->get('search')){
            $data = $data->where($list_terms['search']);
        }
        $list = helper('apex')->perform_filtering($data,$select_array,$request,'customer',Customer::class);
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
    public function search(Request $request)
    {
        $select_array = ['id','name'];
        $data = Customer::select($select_array);
        $data = $data->where('publish',1);
        if($request->get('search')){
            $data = $data->where('name', 'like', '%'.$request->get('search').'%');
        }
        $data = $data->limit(15)->get();
        $items = [];
        foreach ($data as $obj) {
            $items[] = [
                'text' => $obj->name,
                'value' => $obj->id,
            ];
        }
        return response()->json(
            [
                'items' => $items
            ]
        );
    }
    public function save(Request $request)
    {
    	$arr = json_decode($request->data,true);
    	$validator = Validator::make($arr, [
    		'name.value'=>'required|unique:customers,name',
    		//'email.value'=>'required|email|unique:customers,email',
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
    		//'email.value'=>'required|email|unique:customers,email,'.$id,
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
    public function import(Request $request)
    {
        //dd($request->toArray());
        $file = $request->file('file');
        $method = $request->method;
        //dd($file->extension());
        if($file->extension() != 'xlsx' && $file->extension() != 'zip')
        {
            return response()->json([
                'status' => 'file_failed',
                'message' => 'Error: The uploaded file is not valid. Please try again'
            ]);
        }
        else{
            try {
                $import = new CustomerImport($method);
                $import->import($request->file('file'));
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

                 $failures = $e->failures();
                
                 foreach ($failures as $failure) {
                    $msg = $failure->errors();
                    $messages[$failure->row()][$failure->attribute()]['message'] = $msg[0];
                 }
                 return response()->json([
                    'status' => 'failed',
                    'messages' => $messages
                ]);
            }
           return response()->json([
                'status' => 'success',
                'message' => 'Import Completed successfully'
            ]);
        }
    }
}