<?php
namespace App\Modules\Customer\Models;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';
    protected $fillable=['name','slug','line1','line2','pin','state','country','tel'];
    public function customer()
    {
        return $this->belongsTo('App\Modules\Customer\Models\Customer');
    }
}
