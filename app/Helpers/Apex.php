<?php
namespace App\Helpers;
use Validator;
use Konekt\Concord\Facades\Concord;
use Illuminate\Support\Facades\Cache;
class Apex
{
    public function data_filter($data,$filterables,$request)
    {
        $filtered = [];
        foreach ($filterables as $key => $filter)
        {
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
                if($filter['relation'] == 'one2one' || $filter['relation'] == 'many2one')
                {
                    if($filter['type'] == 'select')
                    {
                        $func_name = substr($key,0,strlen($key)-3);
                        $data = $data->filter(function($item) use($request,$key,$func_name,$filter){
                            $it = $item->$func_name()->first();
                            if($it[$filter['filter_column']] == $request->get($key))
                            {
                                return true;
                            }
                            return false;
                        });
                        $filtered[]=
                        [
                            'key' => $key,
                            'value' => $filter['class']::find($request->get($key))->name,//$request->get($key),
                            'name' => $filter['name'],
                            'type' => $filter['type']
                        ];
                    }
                    if($filter['type']=='slider')
                    {
                        $limit = explode(",",$request->get($key));
                        $data = $data->filter(function($item) use($request,$key,$filter,$limit){
                            $it = $item->$key()->first();
                            if($it[$filter['filter_column']] >= $limit[0] && $it[$filter['filter_column']] <=$limit[1])
                            {
                                return true;
                            }
                            return false;
                        });
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
                        $data = $data->filter(function($item) use($request,$key,$filter,$terms){
                            if(isset($filter['relation_func'])){
                                $func_name = $filter['relation_func'];
                               $its = $item->$func_name(); 
                            }
                            else{
                                $its = $item->$key()->get();
                            }
                            //dd($its);
                            $count=0;       
                            foreach ($its as $it) {
                                if(in_array($it[$filter['filter_column']],$terms))
                                {
                                    $count = $count+1;
                                }
                                
                            }
                            if($count == count($terms)){
                                return true;
                            }
                            return false;
                        });
                        $term_names=[];
                        foreach ($terms as $id) {
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
                    if($filter['type'] == 'slider')
                    {
                        $limit = explode(",",$request->get($key));
                        $data = $data->filter(function($item) use($request,$key,$filter,$limit){
                            $related_func = $filter['relation_func'];
                            $pivot_name = $filter['pivot_column'];
                            $its = $item->$related_func()->get();
                            $total = 0;
                            foreach ($its as $it) {
                                $total = $total + $it->pivot->$pivot_name;
                            }
                            if($total >= $limit[0] && $total <=$limit[1])
                            {
                                return true;
                            }
                            return false;
                        });
                        $filtered[]=
                        [
                            'key' => $key,
                            'range' => $limit,
                            'name' => $filter['name'],
                            'type' => $filter['type']
                        ];
                    }
                }
            }
        }
        return ['data'=>$data,'filtered'=>$filtered];
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
                if($arr['type'] == 'slider')
                {
                    $related_func = $arr['relation_func'];
                    $pivot_name = $arr['pivot_column'];
                    $objs = $class::select('id')->get();
                    $val_array = [];
                    if(Cache::has($module.'_'.$pivot_name.'_max') && Cache::has($module.'_'.$pivot_name.'_min'))
                    {
                        $max_val = Cache::get($module.'_'.$pivot_name.'_max');
                        $min_val = Cache::get($module.'_'.$pivot_name.'_min');
                    }
                    else
                    {
                        foreach ($objs as $obj){
                            $its = $obj->$related_func()->get();
                            $temp = 0;
                            foreach ($its as $it) {
                                $temp = $temp + $it->pivot->$pivot_name; 
                            }
                            $val_array[] = $temp;
                        }
                        if(count($val_array) == 0){
                            $val_array = [0];
                        }
                        $max_val = max($val_array);
                        $min_val = min($val_array);
                        Cache::put($module.'_'.$pivot_name.'_max',$max_val);
                        Cache::put($module.'_'.$pivot_name.'_min',$min_val);
                    }
                    
                    $filterable[$key]['max_value'] = ceil($max_val);
                    $filterable[$key]['min_value'] = floor($min_val);
                    $filterable[$key]['range'] = [floor($min_val), ceil($max_val)];
                    $filterable[$key]['default'] = [floor($min_val), ceil($max_val)];
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
            if($s != 'id'){
                $headers[] = ['text'=>$fields[$s]['text'],'value'=>$s];
            }
        }
        $headers[]=['text'=>'Actions','value'=>'actions','align'=>'right'];
        $page = 1;
        if($request->get('page')){
            $page = $request->get('page');
        }
        $rpp = NULL;
        if($request->get('rpp')){
            $rpp = $request->get('rpp');
        }
        foreach ($searcheable as $value) {
            $search[]=[$value,'like','%'.$request->get('search').'%'];
        }
        return [
            'search' => $search,
            'page' => $page,
            'rpp' => $rpp,
            'headers'=>$headers,
            'fields' => $fields,
        ];
    }
    public function perform_filtering($data,$rpp,$page,$select_array,$request,$entity,$class){
        $filterables = $this->get_filterables($entity,$class);
        $data_filter = $this->data_filter($data,$filterables,$request);
        $data = $data_filter['data'];
        $filtered = $data_filter['filtered'];
        $total = $data->count();
        /*if($rpp)
        {
            $data = $data->slice(($page-1)*$rpp,$rpp);
        }
        */
        //dd($data);
        $data = $data->map(
            function ($dat) use ($select_array,$filterables)
            {
                $arr = $dat->toArray();
                $new_arr =[];
                foreach ($select_array as $select_term) {
                    if(array_key_exists($select_term,$filterables) && $filterables[$select_term]['relation'] == 'many2one')
                    {
                        $func_name = substr($select_term,0,strlen($select_term)-3);
                        $new_arr[$select_term] = $dat->$func_name()->first()->name;
                    }
                    elseif (array_key_exists($select_term,$filterables) && $filterables[$select_term]['relation'] == 'one2many') {
                        $related_func = $filterables[$select_term]['relation_func'];
                        $pivot_name = $filterables[$select_term]['pivot_column'];
                        $its = $dat->$related_func()->get();
                        $total = 0;
                        foreach ($its as $it) {
                            $total = $total+ $it->pivot->$pivot_name;
                        }
                        $new_arr[$select_term] = $total;
                    }
                    else
                    {
                        $new_arr[$select_term] = ($arr[$select_term]?$arr[$select_term]:'-');
                    }
                }
                return collect($new_arr)->all();
            }
        );
        $items = [];
        $user = \Auth::user();
        $delete_flag = 0;
        if($user->can('delete_'.$entity)){
            $delete_flag = 1;
        }
        $edit_flag = 0;
        if($user->can('edit_'.$entity)){
            $edit_flag = 1;
        }
        foreach ($data as $dat) {
            $dat['actions'] = [
                'actions'=>1,
                'edit'=>$edit_flag,
                'delete'=>$delete_flag,
                'id' => $dat['id'],
            ];
            $items[] = $dat;
        }
        return [
            'total'=>$total,
            'items' => $items,
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
