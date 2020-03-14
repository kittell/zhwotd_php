<?php

require_once('../functions/config.php');
require_once('../functions/wotd-social.php');
require_once('../../libraries/TwitterAPIExchange.php');

define('TODAY', date('Y-m-d'));
$status = format_wotd_twitter(TODAY);
echo $status;
post_wotd_to_twitter(TODAY);