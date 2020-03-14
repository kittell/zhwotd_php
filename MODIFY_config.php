<?php

// Add the appropriate details below. Save file without MODIFY_ prefix.

define('HOME_DIR', $_SERVER["DOCUMENT_ROOT"]);
define('LIBRARIES_DIR', HOME_DIR . '/libraries');
define('INCLUDE_DIR', HOME_DIR . '/include');
define('FUNCTIONS_DIR', INCLUDE_DIR . '/functions');
define('DISPLAYS_DIR', INCLUDE_DIR . '/displays');
define('TEST_DIR', INCLUDE_DIR . '/test');


define('HOME_DIR_EXTERNAL', 'http://' . $_SERVER["SERVER_NAME"]);
define('INCLUDE_DIR_EXTERNAL', '/include');
define('CSS_DIR', INCLUDE_DIR_EXTERNAL . '/css');


function get_db_login() {
    $db_login['host'] = 'HOST';
    $db_login['db'] = 'DB';
    $db_login['user'] = 'USER';
    $db_login['password'] = 'PASSWORD';
    return $db_login;
}


function get_twitter_login() {
    $twitter_login['consumer_key'] = 'CONSUMER_KEY';
    $twitter_login['consumer_secret'] = 'CONSUMER_SECRET';
    $twitter_login['access_token'] = 'ACCESS_TOKEN';
    $twitter_login['access_token_secret'] = 'ACCESS_TOKEN_SECRET';
    return $twitter_login;
}


function get_facebook_login() {
    $facebook_login['app_secret'] = 'APP_SECRET';
    $facebook_login['app_id'] = 'APP_ID';
    $facebook_login['access_token'] = 'ACCESS_TOKEN';
    return $facebook_login;
}

function get_cron_key_config(){
    return 'CRON_KEY';
}