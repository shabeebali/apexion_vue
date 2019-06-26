<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Konekt\Concord\Facades\Concord;
use \App\User;
use Illuminate\Validation\Rule;
use Konekt\Acl\Models\Role;
use Konekt\Acl\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Validator;

class ConfigController extends Controller
{
    public function menuRender(){
        $user = \Auth::user();
        $modules = Concord::getModules();
        foreach($modules as $key => $value){
            if(config($key.'.menu')){
                if($user->can(config($key.'.menu.permission'))){
                    $temp[]=config($key.'.menu');
                }
            }
        }
        return $temp;
    }
    public function getMenu()
    {
        $user = \Auth::user();
        $items = [];
        if($user->can('list_users')){
            $items[]=[
                'text'=>'Users',
                'route'=>'/settings/users/list',
                'icon'=>'person'
            ];
        }
        if($user->can('list_user_roles')){
            $items[]=[
                'text'=>'User Roles',
                'route'=>'/settings/users/roles/list',
                'icon'=>'supervisor_account'
            ];
        }
        return response()->json([
            'items' => $items
        ]);
    }
    public function users(Request $request){
        $user = \Auth::user();
        $headers=[];
        $select_array = ['id','name','email'];
        $list_terms = helper('apex')->get_list_terms($request,'user',$select_array,['name','email']);
        if($request->get('search')){
            $data = User::where(function($query) use ($list_terms){
               foreach ($list_terms['search'] as $search_string) {
                    $query->orWhere($search_string[0],$search_string[1],$search_string[2]);
                } 
            })->get();
        }
        else{
            $data = User::all();
        }
        $list = helper('apex')->perform_filtering($data,$list_terms['rpp'],$list_terms['page'],$select_array,$request,'user',User::class);
        $json = [
                'items'=>$list['items'],
                'headers'=>$list_terms['headers'],
                'total'=>$list['total'],
                'fields'=>'',
                'filterables'=>$list['filterables'],
                'filtered' => $list['filtered'],
            ];
        if($user->can('create_users')){
            $json['addflag'] = 1;
        }
        else{
            $json['addflag'] = 0;
        }
        
        return response()->json($json);
    }
    public function addUser()
    {
        $roles = Role::select('id','name')->get();
        return response()->json([
            'roles' => $roles
        ]);
    }
    public function saveUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users',
            'email'=> 'required|unique:users|email',
            'password' => 'required|size:8',
            'confirm_password' => 'required|same:password',
            'roles' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $role_ids = explode(",",$request->roles);
        $arr=[]; 
        foreach ($role_ids as $id) {
            $r = Role::find($id);
            $arr[]=$r->name;
        }
        $user->assignRole($arr);
        return response()->json([
            'message' => 'success'
        ]);
    }
    public function editUser(Request $request, $id)
    {
        $user = User::find($id);
        $roles = $user->roles()->get();
        $all_roles = Role::select('id','name')->get();
        $ids = [];
        foreach ($roles as $role) {
            $ids[] = $role->id;
        }
        $formdata = [
            'name' => [
                'value'=>$user->name,
                'error'=>''
            ],
            'email' => [
                'value'=>$user->email,
                'error'=>''
            ],
            'roles' => [
                'items' => $all_roles,
                'error' => '',
                'value' => $ids
            ]
        ];
        return response()->json([
            'formdata' => $formdata
        ]);
    }
    public function updateUser(Request $request,$id)
    {

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
            'email'=> [
                'required',
                Rule::unique('users')->ignore($id),
                'email',
            ],
            'roles' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $role_ids = explode(",",$request->roles);
        $arr=[]; 
        foreach ($role_ids as $id) {
            $r = Role::find($id);
            $arr[]=$r->name;
        }
        $user->syncRoles($arr);
        return response()->json([
            'message' => 'success'
        ]);
    }
    public function changePasswordUser(Request $request,$id)
    {
        $user = User::find($id);
        $validator = Validator::make($request->all(), [
            'password' => 'required|size:8',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'message' => 'success'
        ]);
    }
    public function get_users_filterables()
    {
        $filterables = helper('apex')->get_filterables('user',User::class);
        return response()->json(['filterables'=>$filterables]);
    }
    public function deleteUser(Request $request)
    {
        $ids = explode(",",$request->delete_ids);
        //dd($ids);
        foreach ($ids as $id) {
            $obj =  User::find($id);
            $obj->syncRoles([]);
        }
        User::destroy($ids);
        return response()->json([
            'message'=>'success',
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
        $role = Role::create(['name' => $request->name]);
        $p_ids = explode(",",$request->permission_ids);
        //dd(Permission::find(6));
        $arr=[]; 
        foreach ($p_ids as $id) {
            $p = Permission::find($id);
            $arr[]=$p->name;
        }
        //dd($arr);
        $role->givePermissionTo($arr);
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function editRole($id)
    {
        $role = Role::find($id);
        $role_permissions = $role->permissions()->get();
        $r_p_ids = [];
        foreach ($role_permissions as $r_p) {
            $r_p_ids[]=$r_p->id;
        }
        //dd($r_p_ids);
        $all_permissions = Permission::all();
        foreach ($all_permissions as $a_p) {
            if(in_array($a_p->id, $r_p_ids)){
                $a_p->value = 1;
            }
            else{
                $a_p->value = 0;
            }
        }
        return response()->json([
            'name' => $role->name,
            'permissions' => $all_permissions,
        ]);
    }
    public function updateRole(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('roles')->ignore($id)
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'failed',
                'request'=>$request->all(),
                'errors'=>$validator->errors(),
            ]);
        }
        $role = Role::find($id);
        $p_ids = explode(",",$request->permission_ids);
        //dd($p_ids);
        $arr=[]; 
        foreach ($p_ids as $id) {
            $p = Permission::find($id);
            $arr[]=$p->name;
        }
        //dd($arr);
        $role->syncPermissions($arr);
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function user_roles(Request $request){
        $user = \Auth::user();
        $headers=[];
        $select_array = ['id','name'];
        $list_terms = helper('apex')->get_list_terms($request,'role',$select_array,['name']);
        if($request->get('search')){
            $data = Role::where(function($query) use ($list_terms){
               foreach ($list_terms['search'] as $search_string) {
                    $query->orWhere($search_string[0],$search_string[1],$search_string[2]);
                } 
            })->get();
        }
        else{
            $data = Role::all();
        }
        $list = helper('apex')->perform_filtering($data,$list_terms['rpp'],$list_terms['page'],$select_array,$request,'role',Role::class);
        $json = [
                'items'=>$list['items'],
                'headers'=>$list_terms['headers'],
                'total'=>$list['total'],
                'fields'=>'',
                'filterables'=>$list['filterables'],
                'filtered' => $list['filtered'],
            ];
        if($user->can('create_users')){
            $json['addflag'] = 1;
        }
        else{
            $json['addflag'] = 0;
        }
        return response()->json($json);
    }
    public function delete(Request $request)
    {
        $ids = explode(",",$request->delete_ids);
        //dd($ids);
        foreach ($ids as $id) {
            $obj =  Role::find($id);
            $obj->syncPermissions([]);
        }
        Role::destroy($ids);
        return response()->json([
            'message'=>'success',
        ]);
    }
    public function permissions()
    {
        $permissions = Permission::select('id','name')->get();
        foreach ($permissions as $p) {
            $p->value = '';
        }
        return response()->json([
            'permissions' => $permissions
        ]);
    }
}
