<?php
// !!! File was generated automatically !!!
// - use `artisan admin:export-menu-config` to refresh it from the database.
// - use `artisan admin:import-menu-config` to load it into database.
return [
    [
        'order' => 1,
        'title' => 'Dashboard',
        'icon' => 'fa-bar-chart',
        'uri' => '/',
    ],
    [
        'order' => 100,
        'title' => 'Торговые сети',
        'icon' => 'fa-th-large',
        'uri' => 'trade',
        'roles' => [
            0 => 'administrator',
            1 => 'manager',
        ],
    ],
    [
        'order' => 150,
        'title' => 'Товары',
        'icon' => 'fa-product-hunt',
        'uri' => 'products',
        'roles' => [
            0 => 'administrator',
            1 => 'manager',
        ],
    ],
    [
        'order' => 200,
        'title' => 'Промокоды',
        'icon' => 'fa-barcode',
        'uri' => 'promocodes',
        'roles' => [
            0 => 'administrator',
            1 => 'manager',
        ],
    ],
    [
        'order' => 300,
        'title' => 'Рассылка оповещений',
        'icon' => 'fa-envelope',
        'uri' => 'mails',
        'roles' => [
            0 => 'administrator',
            1 => 'manager',
        ],
    ],
    [
        'order' => 500,
        'title' => 'Журнал промокодов',
        'icon' => 'fa-list-alt',
        'uri' => 'promocode-logs',
        'roles' => [
            0 => 'administrator',
            1 => 'manager',
        ],
    ],
    [
        'order' => 600,
        'title' => 'Журнал IDX',
        'icon' => 'fa-list-alt',
        'uri' => 'idx-logs',
        'roles' => [
            0 => 'administrator',
            1 => 'manager',
        ],
    ],
    [
        'order' => 700,
        'title' => 'Пользователи API',
        'icon' => 'fa-user-secret',
        'uri' => 'api-users',
        'roles' => [
            0 => 'administrator',
            1 => 'manager',
        ],
    ],
  [
    'order' => 1000,
    'title' => 'Admin',
    'icon' => 'fa-tasks',
    'roles' => [0 => 'administrator',],
    'children' => 
    [
      [
        'order' => 1100,
        'title' => 'Users',
        'icon' => 'fa-users',
        'uri' => 'auth/users',
      ],
      [
        'order' => 1200,
        'title' => 'Roles',
        'icon' => 'fa-user',
        'uri' => 'auth/roles',
      ],
//      [
//        'order' => 1300,
//        'title' => 'Permission',
//        'icon' => 'fa-ban',
//        'uri' => 'auth/permissions',
//      ],
      [
        'order' => 1400,
        'title' => 'Menu',
        'icon' => 'fa-bars',
        'uri' => 'auth/menu',
      ],
      [
        'order' => 1500,
        'title' => 'Operation log',
        'icon' => 'fa-history',
        'uri' => 'auth/logs',
      ],
    ],
  ],
//  [
//    'order' => 3,
//    'title' => 'robots.txt',
//    'icon' => 'fa-cog',
//    'uri' => 'robots-txt',
//  ],
];
