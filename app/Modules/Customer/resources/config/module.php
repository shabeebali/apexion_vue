<?php
return [
	'menu' => [
        'route' => '/customers/list',
        'name' => 'Customers',
        'icon' => 'people',
        'permission' => 'list_customers'
    ],
    'fields'=>[
        'customer'=>[
            'id'=>[
                'text'=>'Id',
            ],
            'name'=>[
                'text'=>'name'
            ],
            'email'=>[
                'text'=>'Email'
            ],
        ]
    ],
];