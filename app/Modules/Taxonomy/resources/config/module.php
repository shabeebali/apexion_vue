<?php
return [
    'filterable'=>[
        'category'=>[
            'type_id'=>[
                'name'=>'CategoryType',
                'type'=>'select',
                'relation'=>'many2one',
                'class' => 'App\Modules\Taxonomy\Models\CategoryType',
                'filter_column'=>'id'
            ],
        ]
    ],
    'fields'=>[
        'category'=>[
            'id'=>[
                'text'=>'Id',
            ],
            'name'=>[
                'text'=>'Name'
            ],
            'type_id'=>[
                'text'=>'CategoryType'
            ],
            'code'=>[
                'text' => 'Code'
            ]
        ]
    ]
];
