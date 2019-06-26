<?php
namespace App\Modules\Taxonomy\Providers;

use Konekt\Concord\BaseModuleServiceProvider;
use TorMorten\Eventy\Facades\Events as Eventy;

class ModuleServiceProvider extends BaseModuleServiceProvider
 {
     public function boot()
     {
         parent::boot();
         Eventy::addFilter('products.settings.panel', 'App\Modules\Taxonomy\Listeners\ProductsSettingsPanel@index', 20, 1);
     }
 }
