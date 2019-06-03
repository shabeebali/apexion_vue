<?php
return [
    'filterable'=>[
        'category'=>[
            'type_id'=>[
                'name'=>'Category Type',
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
                'text'=>'Category Type'
            ],
            'code'=>[
                'text' => 'Code'
            ]
        ]
    ]
];
