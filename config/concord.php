<?php

return [
    'modules' => [
        App\Modules\Product\Providers\ModuleServiceProvider::class,
        App\Modules\Customer\Providers\ModuleServiceProvider::class,
        App\Modules\ProductCode\Providers\ModuleServiceProvider::class,
        App\Modules\Settings\Providers\ModuleServiceProvider::class,
        App\Modules\Taxonomy\Providers\ModuleServiceProvider::class,
        App\Modules\Sale\Providers\ModuleServiceProvider::class,
        Konekt\Acl\Providers\ModuleServiceProvider::class,
    ]
];
