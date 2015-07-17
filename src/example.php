<?php
    require_once('./CurlClient.php');

    // flickr API
    // https://www.flickr.com/services/feeds/docs/photos_public/
    $curlClient = new CurlClient('https://api.flickr.com/services/feeds/photos_public.gne');
    $curlClient->addRequestData('format', 'json');
    $curlClient->addRequestData('tags', 'gopro');

    try {
        $response = $curlClient->get();
        echo $response;
    } catch (Exception $e) {
        error_log('get failed: ', $e->getMessage(), "\n");
    } 

    // TODO I need to install 5.5 to use the finally block :-(
    $curlClient->close();
