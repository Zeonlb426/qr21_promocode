<?php

return [
    'forces' => [
        'APP_ENV' => 'production',
        'APP_DEBUG' => false,
        'APP_URL' => 'http://localhost',

        'LOG_CHANNEL' => 'daily',
        'LOG_LEVEL' => 'debug',

        'DB_CONNECTION' => 'pgsql',
        'DB_HOST' => 'postgres',
        'DB_PORT' => 5432,
        'DB_DATABASE' => 'default',
        'DB_USERNAME' => 'postgres',

        'BROADCAST_DRIVER' => 'redis',
        'CACHE_DRIVER' => 'redis',
        'QUEUE_CONNECTION' => 'redis',
        'SESSION_DRIVER' => 'redis',
        'SESSION_LIFETIME' => 120,

        'REDIS_HOST' => 'redis',
        'REDIS_PORT' => 6379,

        'MAIL_MAILER' => 'smtp',
        'MAIL_HOST' => 'mailhog',
        'MAIL_PORT' => 1025,
    ],
];
