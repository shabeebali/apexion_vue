<?php
return [
    'menu' => [
        'route' => '/products/list',
        'name' => 'Products',
        'icon' => 'list_alt',
        'permission' => 'list_products'
    ],
    'permissions'=>[
        'create_product',
        'edit_product',
        'view_product',
        'list_products',
        'delete_product',
    ],
    'filterable'=>[
        'product'=>[
            'gst'=>[
                'name'=>'GST',
                'type'=>'select',
                'relation'=>'none',
                'options'=>[
                    [
                        'text'=>'All',
                        'value'=>'-1',
                    ],
                    [
                        'text'=>'5%',
                        'value'=>'5',
                    ],
                    [
                        'text'=>'12%',
                        'value'=>'12',
                    ],
                    [
                        'text'=>'18%',
                        'value'=>'18',
                    ]
                ],
                'value'=>'-1',
            ],
            'weight'=>[
                'name'=>'Weight',
                'type'=>'slider',
                'relation'=>'none',
            ],
            'mrp'=>[
                'name'=>'MRP',
                'type'=>'slider',
                'relation'=>'none',
            ],
            'categories'=>[
                'name'=>'Categories',
                'type'=>'multiselect',
                'relation'=>'one2many',
                'relation_name'=>'category',
                'filter_column'=>'id',
                'class'=>'App\Modules\Taxonomy\Models\Category',
                'group_class'=>'App\Modules\Taxonomy\Models\CategoryType',
                'group_relation' => 'type'
            ],
            'stock'=>[
                'name'=>'Stock',
                'type'=>'slider',
                'relation'=>'one2many',
                'relation_func'=>'warehouses',
                'pivot_column'=>'stock',
            ]
        ],
    ],
    'fields'=>[
        'product'=>[
            'id'=>[
                'text'=>'Id',
            ],
            'name'=>[
                'text'=>'name'
            ],
            'hsn'=>[
                'text'=>'HSN Code'
            ],
            'sku'=>[
                'text'=>'SKU'
            ],
            'mrp'=>[
                'text'=>'MRP',
            ],
            'gst'=>[
                'text'=>'GST'
            ],
            'landing_price'=>[
                'text'=> 'Landing Price'
            ],
            'general_selling_price'=>[
                'text'=>'Selling Price'
            ],
            'general_selling_dealer'=>[
                'text'=>'Selling Price(Dealer)'
            ],
            'weight'=>[
                'text'=>'Weight'
            ],
            'remarks'=>[
                'text' => 'Remarks'
            ],
            'description'=>[
                'text' => 'Description'
            ],
            'stock'=>[
                'text'=>'Stock',
                'avoid'=>true
            ]
        ]
    ]
];
