<?php
// 1. multi handle
$mh = curl_multi_init();

// 2. add multiple URLs to the multi handle
for ($i = 0; $i < $max_connections; $i++) {
    add_url_to_multi_handle($mh, $url_list);
}

// 3. initial execution
do {
    $mrc = curl_multi_exec($mh, $active);
} while ($mrc == CURLM_CALL_MULTI_PERFORM);

// 4. main loop
while ($active && $mrc == CURLM_OK) {
    // 5. there is activity
    if (curl_multi_select($mh) != -1) {
        // 6. do work
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        // 7. is there info?
        // this means one of the requests were finished
        if ($mhinfo = curl_multi_info_read($mh)) {

            // 8. get the info on the curl handle
            $chinfo = curl_getinfo($mhinfo['handle']);

            // 9. dead link?
            if (!$chinfo['http_code']) {
                $dead_urls []= $chinfo['url'];
            } else if ($chinfo['http_code'] == 404) { // 10. 404?
                $not_found_urls []= $chinfo['url'];
            } else { // 11. working
                $working_urls []= $chinfo['url'];
            }

            // 12. remove the handle
            curl_multi_remove_handle($mh, $mhinfo['handle']);
            curl_close($mhinfo['handle']);

            // 13. add a new url and do work
            if (add_url_to_multi_handle($mh, $url_list)) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
    }
}

// 14. finished
curl_multi_close($mh);

echo "==Dead URLs==\n";
echo implode("\n",$dead_urls) . "\n\n";

echo "==404 URLs==\n";
echo implode("\n",$not_found_urls) . "\n\n";

echo "==Working URLs==\n";
echo implode("\n",$working_urls);

// 15. adds a url to the multi handle
function add_url_to_multi_handle($mh, $url_list) {
    static $index = 0;

    // if we have another url to get
    if ($url_list[$index]) {

        // new curl handle
        $ch = curl_init();

        // set the url
        curl_setopt($ch, CURLOPT_URL, $url_list[$index]);
        // to prevent the response from being outputted
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // follow redirections
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // do not need the body. this saves bandwidth and time
        curl_setopt($ch, CURLOPT_NOBODY, 1);

        // add it to the multi handle
        curl_multi_add_handle($mh, $ch);


        // increment so next url is used next time
        $index++;

        return true;
    } else {

        // we are done adding new URLs
        return false;
    }
}
