<?php

use xPDO\xPDO;

return [
    'mysql_array_options' => [
        xPDO::OPT_TABLE_PREFIX => 'modx_',
        xPDO::OPT_CONNECTIONS => [
            [
                'dsn'      => 'mysql:host=db;dbname=modx;charset=utf8mb4',
                'username' => 'modx',
                'password' => 'modx_password',
                'options' => [
                    xPDO::OPT_CONN_MUTABLE => true,
                ],
                'driverOptions' => [],
            ],
        ],
    ],
];
