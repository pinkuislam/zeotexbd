<?php

return [
    'layout_view' => 'layouts.app',
    'middleware' => ['auth', 'verified', 'valid.auth'],
    'tables' => [
        'created_by' => 'users',
        'updated_by' => 'users',
    ],
    'models' => [
        'created_by' => \App\Models\User::class,
        'updated_by' => \App\Models\User::class,
    ],
];
