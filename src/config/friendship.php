<?php

/**
 * @license MIT
 * @package WalkerChiu\Friendship
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Switch association of package to On or Off
    |--------------------------------------------------------------------------
    |
    | When you set someone On:
    |     1. Its Foreign Key Constraints will be created together with data table.
    |     2. You may need to change the corresponding class settings in the config/wk-core.php.
    |
    | When you set someone Off:
    |     1. Association check will not be performed on FormRequest and Observer.
    |     2. Cleaner and Initializer will not handle tasks related to it.
    |
    | Note:
    |     The association still exists, which means you can still access related objects.
    |
    */
    'onoff' => [
        'rule'     => 0,
        'rule-hit' => 0
    ],

    /*
    |--------------------------------------------------------------------------
    | Lang Log
    |--------------------------------------------------------------------------
    |
    | 0: Don't keep data.
    | 1: Keep data.
    |
    */
    'lang_log' => 0,

    /*
    |--------------------------------------------------------------------------
    | Output Data Format from Repository
    |--------------------------------------------------------------------------
    |
    | null:                  Query.
    | query:                 Query.
    | collection:            Query collection.
    | collection_pagination: Query collection with pagination.
    | array:                 Array.
    | array_pagination:      Array with pagination.
    |
    */
    'output_format' => null,

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    */
    'pagination' => [
        'pageName' => 'page',
        'perPage'  => 15
    ],

    /*
    |--------------------------------------------------------------------------
    | Command
    |--------------------------------------------------------------------------
    |
    | Location of Commands.
    |
    */
    'command' => [
        'cleaner' => 'WalkerChiu\Friendship\Console\Commands\FriendshipCleaner'
    ]
];
