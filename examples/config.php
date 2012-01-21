<?php
$config = array(
    'appId' => 'YOUR_APP_ID',
    'secret' => 'YOUR_APP_SECRET',
    'permissionsArray' => array(
        'publish_stream',
        'read_stream',
        'offline_access',
        'user_groups'
    ),
    'afterLoginUrl' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
);

// Nagpur PHP User group
$sourceId = '107329506051213';
?>