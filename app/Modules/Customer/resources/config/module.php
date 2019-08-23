<?php
return [
	'menu' => [
        'route' => '/customers/',
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