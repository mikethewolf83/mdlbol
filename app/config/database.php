<?php

return [
    'default' => [
        'adapter'  => $_ENV['DB_ADAPTER'],
        'database' => $_ENV['DB_NAME'],
        'username' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
        'host'     => $_ENV['DB_HOST'],
        'type'     => $_ENV['DB_TYPE'],
        'options'   => [
            $_ENV['DB_OPTIONS'],
        ]
    ],
];
