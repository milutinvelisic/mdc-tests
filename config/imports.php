<?php

return [
    'orders' => [
        'label' => 'Import Orders',
        'permission_required' => 'import-orders',
        'files' => [
            'file1' => [
                'label' => 'File 1',
                'headers_to_db' => [
                    'order_date' => [
                        'label' => 'Order Date',
                        'type' => 'date',
                        'validation' => [
                            'required'
                        ]
                    ],
                    'channel' => [
                        'label' => 'Channel',
                        'type' => 'string',
                        'validation' => [
                            'required',
                            'in'
                        ],
                        'in_values' => [
                            'PT',
                            'Amazon',
                            'eBay'
                        ]
                    ],
                    'sku' => [
                        'label' => 'SKU',
                        'type' => 'string',
                        'validation' => [
                            'required',
                            'exists'
                        ],
                        'exists_in' => [
                            'table' => 'products',
                            'column' => 'sku'
                        ]
                    ],
                    'item_description' => [
                        'label' => 'Item Description',
                        'type' => 'string',
                        'validation' => [
                            'nullable'
                        ]
                    ],
                    'origin' => [
                        'label' => 'Origin',
                        'type' => 'string',
                        'validation' => [
                            'required'
                        ]
                    ],
                    'so_num' => [
                        'label' => 'SO#',
                        'type' => 'string',
                        'validation' => [
                            'required'
                        ]
                    ],
                    'cost' => [
                        'label' => 'Cost',
                        'type' => 'double',
                        'validation' => [
                            'required'
                        ]
                    ],
                    'shipping_cost' => [
                        'label' => 'Shipping Cost',
                        'type' => 'double',
                        'validation' => [
                            'required'
                        ]
                    ],
                    'total_price' => [
                        'label' => 'Total Price',
                        'type' => 'double',
                        'validation' => [
                            'required'
                        ]
                    ]
                ],
                'update_or_create' => [
                    'so_num',
                    'sku'
                ]
            ]
        ]
    ],

    // Example of a second import type
    'inventory' => [
        'label' => 'Import Inventory',
        'permission_required' => 'import-inventory',
        'files' => [
            'file1' => [
                'label' => 'Inventory File',
                'headers_to_db' => [
                    'sku' => ['label' => 'SKU','type'=>'string','validation'=>['required','exists','exists_in'=>['table'=>'products','column'=>'sku']]],
                    'quantity' => ['label' => 'Quantity','type'=>'integer','validation'=>['required']],
                ],
                'update_or_create' => ['sku']
            ]
        ]
    ],

    // Third import type, with 2 files
    'shipment' => [
        'label' => 'Import Shipments',
        'permission_required' => 'import-shipments',
        'files' => [
            'file1' => [
                'label' => 'Shipments File 1',
                'headers_to_db' => [
                    'shipment_id'=>['label'=>'Shipment ID','type'=>'string','validation'=>['required','unique'=>'shipments,shipment_id']],
                    'carrier'=>['label'=>'Carrier','type'=>'string','validation'=>['required']],
                ],
                'update_or_create' => ['shipment_id']
            ],
            'file2' => [
                'label' => 'Shipments File 2',
                'headers_to_db' => [
                    'shipment_id'=>['label'=>'Shipment ID','type'=>'string','validation'=>['required']],
                    'tracking_number'=>['label'=>'Tracking','type'=>'string','validation'=>['required','unique'=>'shipment_trackings,tracking_number']],
                ],
                'update_or_create' => ['tracking_number']
            ]
        ]
    ]
];
