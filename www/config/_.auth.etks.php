<?php
/**
 * User: Karel Wintersky
 * Date: 22.09.2018, time: 3:30
 */
 
return [
    'cookie:site_language'  =>  'etks_sitelanguage',
    'auth:cookies'  =>  [
        'user_is_logged'    =>  'u_etks_is_logged',
        'user_permissions'  =>  'u_etks_permissions',
        'user_id'           =>  'u_etks_userid'
    ],
    'auth:session'  =>  [
        'user_is_logged'    =>  'u_etks_is_logged',
        'user_permissions'  =>  'u_etks_permissions',
        'user_id'           =>  'u_etks_userid'
    ]
];
