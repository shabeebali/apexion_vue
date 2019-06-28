<?php
return [
    'menu' => [
        'route' => '/settings/general',
        'name' => 'Settings',
        'icon' => 'settings',
        'permission' => 'access_settings'
    ],
    'filterable'=>[
    	'user'=>[
    		'roles'=>[
                'name'=>'Roles',
                'type'=>'multiselect',
                'relation'=>'one2many',
                'relation_name'=>'roles',
                'filter_column'=>'id',
                'class'=>'Konekt\Acl\Models\Role',
            ]
    	]
    ],
    'fields'=>[
        'user'=>[
        	'id'=>[
        		'text'=>'Id'
        	],
        	'name'=>[
        		'text'=>'Name'
        	],
        	'email'=>[
        		'text'=>'E-mail'
        	]
        ],
        'role'=>[
            'id'=>[
                'text'=>'Id'
            ],
            'name'=>[
                'text'=>'Name'
            ],
        ],
    ],
];
