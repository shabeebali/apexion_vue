<?php
namespace App\Modules\ProductCode\Providers;

use Konekt\Concord\BaseModuleServiceProvider;
use TorMorten\Eventy\Facades\Events as Eventy;
use App\Modules\ProductCode\Listeners\ModifyProductAddForm;
class ModuleServiceProvider extends BaseModuleServiceProvider
 {
     public function boot()
     {
         parent::boot();

         Eventy::addFilter('product.add.form.generated', 'App\Modules\ProductCode\Listeners\ModifyProductAddForm@index', 20, 1);
     }
 }
