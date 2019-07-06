<?php
namespace App\Helpers;
use Validator;
use Konekt\Concord\Facades\Concord;
use Illuminate\Support\Facades\Cache;
class Apex
{
    public function data_filter($data,$request,$module)
    {
        $modules = Concord::getModules();
        $filterables=[];
        foreach($modules as $key => $value){
            if(config($key.'.filterable.'.$module)){
                $temp=config($key.'.filterable.'.$module);
                foreach ($temp as $key => $value) {
                    $filterables[$key] = $value;
                }
            }
        }
        $filtered = [];
        foreach ($filterables as $key => $filter) {
            
            if($request->get($key))
            {
                if($filter['relation'] == 'none')
                {
                    if($filter['type'] == 'select')
                    {
                        $data = $data->where($key,$request->get($key));
                        $filtered[]=
                        [
                            'key' => $key,
                            'value' => $request->get($key),
                            'name' => $filter['name'],
                            'type' => $filter['type']
                        ];
                    }
                    if($filter['type']=='slider')
                    {
                        $limit = explode(",",$request->get($key));
                        $data = $data->whereBetween($key,[$limit[0],$limit[1]]);
                        $filtered[]=
                        [
                            'key' => $key,
                            'range' => $limit,
                            'name' => $filter['name'],
                            'type' => $filter['type']
                        ];
                    }
                }
                if($filter['relation'] == 'one2many' || $filter['relation'] == 'many2many')
                {
                    if($filter['type']=='multiselect')
                    {
                        $terms = explode(",",$request->get($key));
                        //dd($terms);
                        $data = $data->join($filter['pivot_table'],$filter['join_from'],$filter['join_to']);
                        $term_names=[];
                        foreach ($terms as $id) {
                            $data->where($filter['relation_id'],$id);
                            $it = $filter['class']::find($id);
                            $temp = [
                                'name'=>$it->name,
                                'id'=>$it->id
                            ];
                            $term_names[] = $temp;
                        }
                        $filtered[]=
                        [
                            'key' => $key,
                            'terms' => $term_names,
                            'name' => $filter['name'],
                            'type' => $filter['type']
                        ];
                    }
                }
            }
        }
        return ['data'=>$data,'filtered'=>$filtered,'filterables'=>$filterables];
    }
    public function get_filterables($module,$class){
        $modules = Concord::getModules();
        $filterable=[];
        foreach($modules as $key => $value){
            if(config($key.'.filterable.'.$module)){
                $temp=config($key.'.filterable.'.$module);
                foreach ($temp as $key => $value) {
                    $filterable[$key] = $value;
                }
            }
        }
        foreach ($filterable as $key => $arr) {
            if($arr['type'] == 'slider'&& $arr['relation'] == 'none')
            {
                $filterable[$key]['max_value'] = ceil($class::max($key));
                $filterable[$key]['min_value'] = floor($class::min($key));
                $filterable[$key]['range'] = [floor($class::min($key)), ceil($class::max($key))];
                $filterable[$key]['default'] = [floor($class::min($key)), ceil($class::max($key))];
            }
            if($arr['relation'] == 'many2one' || $arr['relation'] == 'one2one')
            {

                if($arr['type'] == 'select')
                {
                    $rel_class = $arr['class'];
                    $items = $rel_class::select('id','name')->get();
                    $temp=[];
                    $temp[] = [
                        'text' => 'All',
                        'value' => '-1',
                    ];
                    foreach ($items as $item) {
                        $temp[] = [
                            'text' => $item->name,
                            'value' => $item->id,
                        ];
                    }
                    $filterable[$key]['options'] = $temp;
                    $filterable[$key]['value'] = '-1';
                }
            }
            if($arr['relation'] == 'many2many' || $arr['relation'] == 'one2many')
            {

                if($arr['type'] == 'multiselect')
                {
                    $rel_class = $arr['class'];
                    $items = $rel_class::all();
                    $temp=[];
                    foreach ($items as $item) {
                        $group = '';
                        if(isset($arr['group_class']))
                        {
                            $func_name = $arr['group_relation'];
                            $group = $item->$func_name()->first()->name;
                        }
                        $temp[] = [
                            'text' => $item->name,
                            'value' => $item->id,
                            'group' =>  $group,
                        ];
                    }
                    $filterable[$key]['options'] = $temp;
                    $filterable[$key]['value'] = '';
                }
            }
        }
        return $filterable;
    }
    public function get_fields($module){
        $modules = Concord::getModules();
        $filterable=[];
        foreach($modules as $key => $value){
            if(config($key.'.fields.'.$module)){
                $temp=config($key.'.fields.'.$module);
                foreach ($temp as $key => $value) {
                    $filterable[$key] = $value;
                }
            }
        }
        return $filterable;
    }
    public function get_list_terms($request,$entity,$select_array,$searcheable){

        $fields = $this->get_fields($entity);

        array_walk($fields, function($value,$key) use ($select_array,&$fields){

            if(in_array($key,$select_array))
            {
                $fields[$key]['selected'] = 'true';
            }

        });
        foreach ($select_array as $s) {
            $s = explode(".",$s);
            //dd($s);
            $s = count($s) > 1? $s[1]:$s[0];
            if($s != 'id'){
                $headers[] = ['text'=>$fields[$s]['text'],'value'=>$s];
            }
        }
        $headers[]=['text'=>'Actions','value'=>'actions','align'=>'right'];
        $page = 1;
        foreach ($searcheable as $value) {
            $search[]=[$value,'like','%'.$request->get('search').'%'];
        }
        return [
            'search' => $search,
            'headers'=>$headers,
            'fields' => $fields,
        ];
    }
    public function perform_filtering($data,$select_array,$request,$entity,$class){
        $data_filter = $this->data_filter($data,$request,$entity);
        $data = $data_filter['data'];
        $filtered = $data_filter['filtered'];
        $filterables = $data_filter['filterables'];
        if($request->sortby)
        {
            $data = $data->orderBy($request->sortby,$request->descending?'desc':'asc');
        }
        $data = $data->paginate($request->rpp);
        $user = \Auth::user();
        $delete_flag = 0;
        if($user->can('delete_'.$entity)){
            $delete_flag = 1;
        }
        $edit_flag = 0;
        if($user->can('edit_'.$entity)){
            $edit_flag = 1;
        }
        $total = $data->toArray()['total'];
        $data = $data->map(
            function ($dat) use ($select_array,$filterables,$edit_flag,$delete_flag)
            {
                $arr = $dat->toArray();
                $new_arr =[];
                foreach ($select_array as $select_term) {
                    $select_term = explode(".",$select_term);
                    $select_term = count($select_term) > 1? $select_term[1]:$select_term[0];
                    if(array_key_exists($select_term,$filterables) && $filterables[$select_term]['relation'] == 'many2one')
                    {
                        $func_name = substr($select_term,0,strlen($select_term)-3);
                        $new_arr[$select_term] = $dat->$func_name()->first()->name;
                    }
                    else
                    {
                        $new_arr[$select_term] = ($arr[$select_term]?$arr[$select_term]:'-');
                    }
                }
                $new_arr['actions'] = [
                    'actions'=>1,
                    'edit'=>$edit_flag,
                    'delete'=>$delete_flag,
                    'id' => $dat['id'],
                ];
                return collect($new_arr)->all();
            }
        );
        return [
            'total'=>$total,
            'items' => $data,
            'filterables'=> $filterables,
            'filtered' => $filtered,
        ];
    }
    public function make_unique_code($pc,$id=NULL){
        $count = 0;
        $pc = $pc.str_pad($count, 3,0,STR_PAD_LEFT);
        if(!$id){

            $data['sku'] = $pc;
            while(Validator::make($data,['sku'=>'unique:products,sku'])->fails()){
                $pc = substr($pc, 0, -3);
                $count = $count + 1;
                $pc = $pc.str_pad($count, 3,0,STR_PAD_LEFT);
                $data['sku'] = $pc;
            }
        }
        else{
            $data['sku'] = $pc;
            while(Validator::make($data,['sku'=>'unique:products,sku,'.$id])->fails()){
                $pc = substr($pc, 0, -3);
                $count = $count + 1;
                $pc = $pc.str_pad($count, 3,0,STR_PAD_LEFT);
                $data['sku'] = $pc;
            }
        }
        return $pc;
    }
    public function update_next_code($cat_type)
    {
        if($cat_type->autogen && $cat_type->in_pc)
        {
            $cc = $cat_type->next_code;
            $ct = $cat_type->code_type;
            $cl = $cat_type->code_length;
            $ct_arr = explode('-',$ct);
            $cc_arr = str_split($cc);
            $jump_next = 1;
            for($i=$cl-1 ; $i>=0 ; $i--){
                if($ct_arr[$i] == 'alpha'){
                    if($jump_next){
                        $curr = $cc_arr[$i];
                        $next = chr(((ord($cc_arr[$i])-65+1)%26)+65);
                        $cc_arr[$i] = $next;
                    }
                    if($curr== 'Z' && $next == 'A'){
                        $jump_next = 1;
                    }
                    else{
                        $jump_next = 0;
                    }
                }
                else{
                    if($jump_next){
                        $curr = $cc_arr[$i];
                        $next = chr(((ord($cc_arr[$i])-48+1)%10)+48);
                        $cc_arr[$i] = $next;
                    }
                    if($curr== '9' && $next == '0'){
                        $jump_next = 1;
                    }
                    else{
                        $jump_next = 0;
                    }
                }
            }
            $cc = implode("",$cc_arr);
            $cat_type->next_code = $cc;
            $cat_type->save();
        }
        return NULL;
    }
}
