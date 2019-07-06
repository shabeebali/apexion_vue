<?php
namespace App\Modules\Settings\Models;
use Illuminate\Database\Eloquent\Model;

class GeneralSettings extends Model
{
	protected $fillable = ['id','zone'];
    protected $table = 'general_settings';
}
