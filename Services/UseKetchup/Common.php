<?php
require_once 'HTTP/Request2.php';

abstract class Services_UseKetchup_Common
{
    protected $password;
    protected $username;

    protected $apiToken;

    protected $client;

    protected $endpoint = 'http://useketchup.com/api/v1';

    public function debugCall()
    {
        return array(
            'event'  => $this->client->getLastEvent(),
            'url'    => (string) $this->client->getUrl(),
            'data'   => $this->client->getBody(),
            'method' => $this->client->getMethod(),
        );
    }

    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    protected function guessId($var)
    {
        if ($var instanceof stdClass) {
            return $var->shortcode_url;
        }
        return $var;
    }

    protected function makeRequest($url, $method = HTTP_Request2::METHOD_GET, $data = null)
    {
        if ($this->apiToken !== null) {
            $url .= '?u=' . $this->apiToken;
        }

        $req = $this->client = new HTTP_Request2;
        $req
            ->setHeader('Content-Type: application/json')
            ->setAuth($this->username, $this->password)
            ->setMethod($method)
            ->setUrl($this->endpoint . $url);

        if ($data !== null) {
            $req->setBody($data);
        }

        $resp = $req->send();

        return $resp;
    }

    protected function parseResponse(HTTP_Request2_Response $resp)
    {
        $body = $resp->getBody();

        $headers = $resp->getHeader();
        if (isset($headers['content-type'])) {
            if ($headers['content-type'] == 'text/calendar; charset=utf-8') {
                return $body;
            }
        }

        switch ($body) {
        case 'Access Denied':
            throw new RuntimeException("API response: {$body}");
        case 'Item Deleted Successfully':
        case 'Meeting Deleted Successfully':
            return $body;
        default:
            $resp = json_decode($body);
            if (isset($resp->status)) {
                switch ($resp->status) {
                case 'internal_server_error':
                    throw new RuntimeException($resp->message);
                }
            }
            return $resp;
        }
    }
}