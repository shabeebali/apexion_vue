<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Modules\Product\Models\Product;

class ProductsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = Product::all();
        $data = $data->map(function($item,$key){

        });
    }
}
