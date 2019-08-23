<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use App\Modules\Customer\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
class CustomerImport implements ToCollection, WithHeadingRow 
{
	use Importable;
    /**
    * @param Collection $collection
    */
    protected $method ='';

    public function __construct($method)
    {
    	$this->method = $method;
    }
    public function collection(Collection $rows)
    {
    	if($this->method == 'create')
    	{
	        $val_array = [
	            '*.name' => 'required|unique:customers,name',
	        ];
	        $rows = $rows->toArray();
	        $validator = Validator::make($rows, $val_array, [], []);
	        if($validator->fails()){
	        	$e1 = new IlluminateValidationException($validator->errors());
	        	$e = $validator;
		        $failures = [];
		        foreach ($e->errors()->toArray() as $attribute => $messages) {
		            $row           = strtok($attribute, '.');
		            $attributeName = strtok('');
		            $attributeName = $attributes['*.' . $attributeName] ?? $attributeName;
		            $failures[] = new Failure(
		                $row,
		                $attributeName,
		                str_replace($attribute, $attributeName, $messages),
		                $rows[$row]
		            );
		        }
		        throw new \Maatwebsite\Excel\Validators\ValidationException(
		            $e1,
		            $failures
		        );
		    }
	    	foreach ($rows as $row) {

	    		$fields = helper('apex')->get_fields('customer');
		        $obj = new Customer;
		        foreach ($fields as $key => $value) {
		        	if(isset($row[$key])){
		        		$obj->$key = $row[$key];
		        	}
		        }
		        $obj->name = trim($obj->name);
		        $obj->slug = Str::slug($obj->name,'_');
		        $obj->publish = $row['publish'] ? $row['publish'] : 0 ;
	        	$obj->tally = $row['tally'] ? $row['tally'] : 0 ;
		        $obj->save();
	    	}
    	}
    	if($this->method == 'update')
    	{
	    	
    	}
    }
}
