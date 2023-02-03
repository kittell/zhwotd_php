<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
require_once('wotd-functions.php');
require_once('wotd-formatting.php');
require_once('queries.php');
require_once(LIBRARIES_DIR.'/TwitterAPIExchange.php');
require_once(LIBRARIES_DIR.'/php-graph-sdk-5.x/src/Facebook/autoload.php');
require_once(LIBRARIES_DIR.'/TootoPHP/autoload.php');

/* FORMATTING */

function format_wotd_social_message($d) {    
    $wotd_entry = get_wotd_entry($d, 0);
    $term = get_query_part($wotd_entry, 'term');
    $pinyin = get_query_part($wotd_entry, 'pinyin');
    $definition = get_query_part($wotd_entry, 'definition');
    $hsk = get_query_part($wotd_entry, 'hsk');
    
    $result = 'Chinese word of the day, ' . $d . ':' . "\n";
    $result .= $term . ' (' . $pinyin . ')' . ' - ' . $definition;
    
    if ($hsk != '' && $hsk != 0) {
        $result .= "\n";
        $result .= 'HSK level: #HSK'. $hsk;
    }
    
    return $result;
}

/* FACEBOOK */

function format_wotd_facebook($d) {
    $message = format_wotd_social_message($d);
    $link = $link = get_wotd_url($d);
    $result = [
        'link' => $link,
        'message' => $message
    ];
    return $result;
}

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

function post_facebook_status($status) {
    $settings = get_facebook_settings();
    $fb = new Facebook\Facebook($settings);
    
    // Source: https://adamboother.com/blog/automatically-posting-to-a-facebook-page-using-the-facebook-sdk-v5-for-php-facebook-api/
    $page_access_token = get_facebook_access_token();
    
    try {
        $response = $fb->post('/me/feed', $status, $page_access_token);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: '.$e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: '.$e->getMessage();
        exit;
    }
    //$graph_node = $response->getGraphNode();
    return $response;
}

function post_wotd_to_facebook($d) {
    // Post the status to Facebook
    $status = format_wotd_facebook($d);
    $response = post_facebook_status($status);
    
    return $response;
}


/* TWITTER */

function format_wotd_twitter($d) {
    $message = format_wotd_social_message($d);
    $link = $link = get_wotd_url($d);
    $result = $message . "\n" . $link;
    return $result;
}

function get_twitter_settings() {
    $twitter_login = get_twitter_login();
    $settings = array(
        'oauth_access_token' => $twitter_login['access_token'],
        'oauth_access_token_secret' => $twitter_login['access_token_secret'],
        'consumer_key' => $twitter_login['consumer_key'],
        'consumer_secret' => $twitter_login['consumer_secret']
    );
    return $settings;
}

function post_twitter_status($status) {
    // Source: https://stackoverflow.com/a/15314662/752784
    // https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/post-statuses-update
    $url = "https://api.twitter.com/1.1/statuses/update.json";
    $settings = get_twitter_settings();
    $postfields = array('status' => $status);
    $request_method = 'POST';
    $twitter = new TwitterAPIExchange($settings);
    
    $response = $twitter->buildOauth($url, $request_method)->setPostfields($postfields)->performRequest();
    return $response;
}

function post_wotd_to_twitter($d) {
    // Post the status to Twitter
    $status = format_wotd_twitter($d);
    $response = post_twitter_status($status);
    
    return $response;
}


/* MASTODON */
// https://framagit.org/MaxKoder/TootoPHP/-/blob/master/1-register_app.php
$tootoPHP = new TootoPHP\TootoPHP('universeodon.com');

$app = $tootoPHP->registerApp('TootoPHP', 'http://max-koder.fr');
if ( $app === false) {
    throw new Exception('Problem during register app');
}

echo $app->getAuthUrl();



/* QUERIES */

function get_wotd_social_id($d, $social_type) {
    $wotd_entry = get_wotd_by_date($d);
    
    if ($social_type == 'twitter') {
        $part = 'twitter_id';
    } elseif ($social_type == 'facebook') {
        $part = 'facebook_id';
    }
    $id = get_query_part($wotd_entry, $part);
    return $id;
}

function is_social_posted_yet($d, $social_type) {
    $id = get_wotd_social_id($d, $social_type);
    $posted = false;
    if ($id != 0 && $id != '') {
        $posted = true;
    }
    echo "<p>is_social_posted: ";
    var_dump($social_type);
    var_dump($id);
    var_dump($posted);
    echo "</p>";
    
    return $posted;
}

function get_social_id_from_json($social_type, $response) {
    // Get the post id from the JSON response
    // Source: https://stackoverflow.com/a/17865683/752784
    // https://www.php.net/manual/en/function.json-decode.php
    $json_response = json_decode($response);
    if ($social_type == 'twitter') {
        $id = $json_response->id;
    } elseif ($social_type == 'facebook') {
        // The id returned is two parts: page_post
        $graph_node = $response->getGraphNode();
        $page_post_id = $graph_node['id'];
        $pieces = explode("_", $page_post_id);
        $id = $pieces[1];
    }
    return $id;
}

function update_social_id($d, $social_type, $response) {
    if ($social_type == 'twitter') {
        $col = 'twitter_id';
    } elseif ($social_type == 'facebook') {
        $col = 'facebook_id';
    }
    
    $id = get_social_id_from_json($social_type, $response);
    $query = "UPDATE wotd SET $col = '$id' WHERE date = '$d'";
//     var_dump($query);
    update_query($query);
    return $id;
}
