<?php
require_once 'HTTP/Request2.php';

class Services_UseKetchup_Common
{
    protected $password;
    protected $username;

    protected $apiToken;

    protected $endpoint = 'http://useketchup.com/api/v1';

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

    protected function makeRequest($url, $method = HTTP_Request2::METHOD_GET, $data = null)
    {
        if ($this->apiToken !== null) {
            $url .= '?u=' . $this->apiToken;
        }

        $req = new HTTP_Request2;
        $req
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
        //var_dump($resp->getStatus());
        return json_decode($resp->getBody());
    }
}