<?php
namespace App\Modules\Customer\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    public function addresses()
    {
        return $this->hasMany('App\Modules\Customer\Models\Address');
    }
    public function phones()
    {
    	return $this->hasMany('App\Modules\Customer\Models\Phone');
    }
}
