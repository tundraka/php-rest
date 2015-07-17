<?php

require_once("./PostUtils.php");

/**
 * Will provide a cURL connection. Ther are several default option set.
 * With help from:
 * http://thehungrycoder.com/general/how-to-submit-a-form-using-php-curl-fsockopen.html
 * http://codular.com/curl-with-php
 */
class CurlClient {

    /**
     * The URL to which this client instance is connecting, stored for logging.
     */
    private $url;

    /**
     * The actual curl client.
     */
    private $curlConnection;

    /**
     * The list of post parameters that will be send in this call.
     */
    private $requestData;

    public function __construct($url) {
        // TODO can we also validate that we have a valid URL?
        if(!PostUtils::hasData($url)) {
            throw new InvalidArgumentException("Invalid connection URL: $url");
        }

        $this->requestData = [];
        $this->url = $url;

        // Not sure if let this to the user or do it automatically here.
        $this->init();
    }

    private function init() {
        // TODO let's create this connection when we are ready to make a call, not
        // from the moment the object is created.
        $this->curlConnection = curl_init($this->url);

        // TODO see: curl_setopt_array
        // TODO let the client set this params.
        $this->setOption(CURLOPT_CONNECTTIMEOUT, 7);
        $this->setOption(CURLOPT_TIMEOUT, 5);
        $this->setOption(CURLOPT_USERAGENT, "PHP 5.6: Custom cURL client.");
        $this->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->setOption(CURLOPT_SSL_VERIFYPEER, true);
        $this->setOption(CURLOPT_FOLLOWLOCATION, 1);
    }

    public function setOption($option, $value) {
        // TODO If this is going to be public, which I think it should be, 
        // let's validate that we have a connection made.
        curl_setopt($this->curlConnection, $option, $value);
    }

    public function addRequestData($variable, $value) {
        if (!(PostUtils::hasData($variable) || PostUtils::hasData($value))) {
            throw new InvalidArgumentException("Adding a post value requires a valid key/value pair");
        }

        $this->requestData[$variable] = $value;
    }

    public function post() {
        // Not sure if needed.
        $this->setOption(CURLOPT_POST, true);

        // TODO we need to validate that we have a connection open.
        // Not sure how does curl validates that that's the case.
        // See also get method
        $requestData = $this->prepareRequestData();
        if (PostUtils::hasData($requestData)) {
            $this->setOption(CURLOPT_POSTFIELDS, $requestData);
        }

        return $this->execute();
    }

    public function get() {
        // TODO we need to validate that we have a connection open.
        // Not sure how does curl validates that that's the case.
        // See also post method
        $requestData = $this->prepareRequestData();
        // TODO it's being a while, but when we make a get we don't necessarily 
        // need request data. So this check should be reviewed.
        // TODO OR if we don't have the connection already made, then store 
        // this in an array, then when the connection is open we pass it.
        if (PostUtils::hasData($requestData)) {
            $this->setOption(CURLOPT_URL, $this->url . "?$requestData");
        }

        return $this->execute();
    }

    public function close() {
        // TODO in a similar way, we need to indicate (if we are supposed to take care of this)
        // that the connection was close.
        // TODO let's make this function silent. Or is it silent by default?
        curl_close($this->curlConnection);
    }

    private function execute() {
        $result = curl_exec($this->curlConnection);

        // If we received a boolean and it's false, then we got an issue.
        if (is_bool($result) && !$result) {
            // We report what the issue was.
            $this->debugInfo();

            // we close the connection.
            $this->close();

            throw new RuntimeException("Call to resouce '" . $this->url . "' failed.");
        } else {
            // we close the connection.
            $this->close();

            return $result;
        }
    }

    private function prepareRequestData() {
        $requestData = "";
        $requestElements = [];

        // we iterate over all post attributes.
        foreach ($this->requestData as $variable => $value) {
            $requestElements[] = "$variable=$value";
        }

        // If we have any post information we concatenate it with &'s
        if (count($requestElements) > 0) {
            $requestData = implode('&', $requestElements);
        }

        return $requestData;
    }

    private function debugInfo() {
        // TODO this should be part of the exception thrown.
        error_log(curl_getinfo($this->curlConnection, CURLINFO_EFFECTIVE_URL));
        /*
        $curlInfo = curl_getinfo($this->curlConnection);

        error_log(implode(":", $curlInfo));
         */
    }
}
