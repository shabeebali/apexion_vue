<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use App\Modules\Taxonomy\Models\Category;
use App\Modules\Taxonomy\Models\CategoryType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class CategoriesImport implements ToCollection, WithHeadingRow 
{
    use Importable;
    /**
    * @param Collection $collection
    */
    protected $type_id = 0;
    public function __construct($type_id)
    {
    	$this->type_id = $type_id;
    }
    public function collection(Collection $rows)
    {
        $category_type = CategoryType::find($this->type_id);
        $val_array = [
            '*.name' => [
                'required',
                Rule::unique('categories')->where('type_id',$this->type_id),
            ],
            '*.code' => [
                Rule::requiredIf($category_type->in_pc),
                'size:'.$category_type->code_length,
                Rule::unique('categories')->where('type_id',$this->type_id),
            ]
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
    		$obj = new Category;
	        $obj->name = trim($row['name']);
	        $obj->slug = Str::slug($row['name'],'_');
	        $obj->code = $row['code'];
            $obj->type_id = $this->type_id;
	        $obj->save();
    	}
    }
}
