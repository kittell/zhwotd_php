<?php

require_once 'wotd-social.php';
require_once 'wotd-functions.php';
require_once 'queries.php';

define('TODAY', date('Y-m-d'));

function social_cron() {
    // Get cron key from config file
    $cron_key_config = get_cron_key();
    
    // Get cron key from database
    $cron_key_db = '';
    $query = "SELECT * FROM config WHERE attribute='cron_key'";
    $row = single_query($query);
    $cron_key_db = get_query_part($row, 'value');
    
    // Match keys
    if ($cron_key_config == $cron_key_db) {
        cron_twitter_post();
        cron_facebook_post();
    }
    
    // TODO: write to log?
}

function cron_twitter_post() {
    $posted = is_social_posted_yet(TODAY, 'twitter');
    
    // TODO: handle: string(60) "{"errors":[{"code":187,"message":"Status is a duplicate."}]}"
    if (!$posted) {
        $response = post_wotd_to_twitter(TODAY);
        $id = update_social_id(TODAY, 'twitter', $response);
        echo "Twitter ID " . $id;
    }
    
}

function cron_facebook_post() {
    $posted = is_social_posted_yet(TODAY, 'facebook');
    if (!$posted) {
        $response = post_wotd_to_facebook(TODAY);
        $id = update_social_id(TODAY, 'facebook', $response);
        echo "Facebook ID " . $id;
    }
    
}

social_cron();