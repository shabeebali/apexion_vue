<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Konekt\Concord\Facades\Concord;
use \App\User;
use Konekt\Acl\Models\Role;
use Konekt\Acl\Models\Permission;
use Validator;

class ConfigController extends Controller
{
    public function menuRender(){
        $modules = Concord::getModules();
        foreach($modules as $key => $value){
            if(config($key.'.menu')){
                $temp[]=config($key.'.menu');
            }
        }
        return $temp;
    }
    public function users(Request $request){
        $page = 1;
        if($request->get('page')){
            $page = $request->get('page');
        }
        $rpp = NULL;
        if($request->get('rpp')){
            $rpp = $request->get('rpp');
        }
        $data = User::where('name','like','%'.$request->get('search').'%')->get();
        $total = $data->count();
        if($rpp)
        {
            $data = $data->slice(($page-1)*$rpp,$rpp);
        }
        $users=[];
        foreach ($data as $item) {
            $temp = ['name'=>$item->name,'email'=>$item->email];
            $users[]=$temp;
        }
        $headers = [
            [
                'text'=>'Name',
                'value'=>'name',
            ],
            [
                'text'=>'Email',
                'value'=>'email',
            ],
        ];
        return response()->json(['items'=>$users,'headers'=>$headers,'total'=>$total]);
    }
    public function addRoleConfig(){
        return response()->json([
            'config'=>[
                'popOut'=>true,
                'headline'=>'Add Role',
                'route'=>'/acl/role/add',
            ],
            'formFields'=>[
                [
                    'textField'=>true,
                    'name'=>'name',
                    'label'=>'Name*',
                    'required'=>true,
                    'errormsg'=>'',
                ],
            ],
        ]);
    }
    public function addRole(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $permission = Role::create(['name' => $request->name]);
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function roles(Request $request){
        $rpp = NULL;
        if($request->get('rpp')){
            $rpp = $request->get('rpp');
        }
        if($rpp){
            $data = Role::where('name','like','%'.$request->get('search').'%')->paginate($rpp);
        }
        else{
            $all=Role::all()->count();
            $data=Role::where('name','like','%'.$request->get('search').'%')->paginate($all);
        }

        $headers=[
            [
                'text'=>'id',
                'value'=>'id',
                'class'=>'d-none',
            ],
            [
                'text'=>'Name',
                'value'=>'name',
            ],
            [
                'text'=> 'Actions',
                'value'=>'actions',
                'sortable'=>false,
                'align'=>'center',
            ]
        ];
        $roles=[];
        if($data->count() > 0){
            foreach ($data as $item) {
                $roles[]=[
                    'id'=>$item->id,
                    'name'=>$item->name,
                    'actions'=>[
                        'actions'=>true,
                        'edit'=>true,
                        'delete'=>true,
                        'edit_route'=>'/acl/role/edit/'.$item->id,
                        'view_route'=>'/acl/role/view/'.$item->id,
                    ],
                ];
            }
        }
        return response()->json(['items'=>$roles,'headers'=>$headers,'total'=>$data->total()]);
    }
}
