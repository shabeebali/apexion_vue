<?php
namespace App\Helpers;
use Konekt\Concord\Facades\Concord;
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
                        $data = $data->filter(function($item) use($request,$key,$filter,$terms){
                            $its = $item->$key()->get();
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
                        $filtered[]=
                        [
                            'key' => $key,
                            'terms' => $terms,
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
            if($arr['type'] == 'slider')
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
                    if(array_key_exists($select_term,$filterables) && $filterables[$select_term]['relation'] != 'none')
                    {
                        $func_name = substr($select_term,0,strlen($select_term)-3);
                        $new_arr[$func_name] = $dat->$func_name()->first()->name;
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
        foreach ($data as $dat) {
            $dat['actions'] = [
                'actions'=>true,
                'edit'=>true,
                'delete'=>true,
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
}
