<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Modules\Taxonomy\Models\Category;
use App\Modules\Taxonomy\Models\CategoryType;

class CategoriesExport implements FromCollection, WithHeadings
{
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data = '';
    public function collection()
    {
        $data = Category::all();
        $data = $data->map(function($item,$key){
        	$item['type'] = CategoryType::find($item->type_id)->name;
        	return $item;
        });
        $this->data = $data;
        return $data;
    }
    public function headings(): array
    {
        $data = Category::first();
        $data['type'] = 1;
        $arr = $data->toArray();
        return array_keys($arr);
    }
}
