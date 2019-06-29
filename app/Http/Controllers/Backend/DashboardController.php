<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Customer\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Modules\Product\Events\SampleEvent;
use TorMorten\Eventy\Facades\Events as Eventy;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
class DashboardController extends Controller
{
    public function dashboard()
    {
    	$user = \Auth::user();
    	return response()->json([
            'pending_count'=> $user->can('approve_product') ? Product::where('publish',0)->get()->count() : 0,
            'tally_count'=> $user->can('sync_tally') ? Product::where([['tally','=',0],['publish','=',1]])->get()->count() : 0,
            'products_count'=> Product::where('publish','=',1)->get()->count(),
            'customers_count' => Customer::get()->count(),
        ]);
    }
    public function quote()
    {
    	$client = new \GuzzleHttp\Client();
		$response = $client->request('GET', 'http://quotes.rest/qod.json?category=inspire');
		return response()->json([
			'data'=> $response
		]);
    }
}
