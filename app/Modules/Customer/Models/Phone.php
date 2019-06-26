<?php
namespace App\Modules\Customer\Models;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $table = 'phones';
    protected $fillable = ['phone'];
    public function customer()
    {
        return $this->belongsTo('App\Modules\Customer\Models\Customer');
    }
}
