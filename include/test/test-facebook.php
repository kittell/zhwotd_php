<?php

require_once('../functions/config.php');
require_once('../../libraries/php-graph-sdk-5.x/src/Facebook/autoload.php');

function get_facebook_settings() {
    $facebook_login = get_facebook_login();
    $settings = array(
        'app_id' => $facebook_login['app_id'],
        'app_secret' => $facebook_login['app_secret'],
        'default_graph_version' => 'v2.2',
    );
    return $settings;
}

function get_facebook_access_token() {
    $facebook_login = get_facebook_login();
    return $facebook_login['access_token'];
}

$settings = get_facebook_settings();
$fb = new Facebook\Facebook($settings);

// Source: https://adamboother.com/blog/automatically-posting-to-a-facebook-page-using-the-facebook-sdk-v5-for-php-facebook-api/
$test_status = "Chinese word of the day, 2019-12-25*:\r\n荒诞 (huāngdàn)  - (adj) preposterous, incredible";
$test_link = 'http://www.zhwotd.com/date/2019-12-25';
$post_link = [
    'link' => $test_link,
    'message' => $test_status
];
$page_access_token = get_facebook_access_token();

try {
    $response = $fb->post('/me/feed', $post_link, $page_access_token);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: '.$e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: '.$e->getMessage();
    exit;
}
$graph_node = $response->getGraphNode();
var_dump($graph_node);
var_dump($response);