<?php

namespace Synaq\HttpClient;

class Response {

    /**
     * The body of the response without the headers block
     *
     * @var string $body
     **/
    private $body = '';

    /**
     * An associative array containing the response's headers
     *
     * @var array
     **/
    private $headers = array();

    /**
     * Accepts the result of a curl request as a string
     *
     * @param string $response
     **/
    public function __construct($response, $skipHeaders = false) {
        // Headers regex
        $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';

        // Extract headers from response
        preg_match_all($pattern, $response, $matches);
        $headersString = array_pop($matches[0]);
        $headers = explode("\r\n", str_replace("\r\n\r\n", '', $headersString));

        // Remove headers from the response body
        $this->body = str_replace($headersString, '', $response);

        // Extract the version and status from the first header
        $versionAndStatus = array_shift($headers);
        preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $versionAndStatus, $matches);
        $this->headers['Http-Version'] = $matches[1];
        $this->headers['Status-Code'] = $matches[2];
        $this->headers['Status'] = $matches[2].' '.$matches[3];

        // Convert headers into an associative array
        foreach ($headers as $header) {
            preg_match('#(.*?)\:\s(.*)#', $header, $matches);
            $this->headers[$matches[1]] = $matches[2];
        }
    }

    /**
     * Returns the response body
     *
     * @return string
     **/
    public function __toString() {

        return $this->body;
    }

    public function getHeaders() {

        return $this->headers;
    }

    public function getBody() {

        return $this->body;
    }

}