<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Friendship
    |--------------------------------------------------------------------------
    |
    */

    'user_id_a' => 'User A',
    'user_id_b' => 'User B',
    'state'     => 'State',
    'flag_a'    => 'Flag A',
    'flag_b'    => 'Flag B',

    'list' => 'list',

    'set' => [
        'header' => 'Change flag',
        'body'   => 'Are you sure you want to change the :value?'
    ],
    'limit' => [
        'header' => 'Limit User',
        'body'   => 'Are you sure you want to limit this user?'
    ],
    'block' => [
        'header' => 'Block User',
        'body'   => 'Are you sure you want to block this user? This friendship will be deleted.'
    ],
    'delete' => [
        'header' => 'Delete Friendship',
        'body'   => 'Are you sure you want to delete this friendship?'
    ]
];
