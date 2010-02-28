<?php
class Services_UseKetchup_User extends Services_UseKetchup_Common
{
    public function add($user)
    {
        $data = json_encode($user);
        $resp = $this->makeRequest(
            '/users.json',
            HTTP_Request2::METHOD_POST,
            $data
        );
        $data = $this->parseResponse($resp);
        if ($data !== null) {
            return true;
        }
        return false;
    }

    public function update($data)
    {
        $data = json_encode($data);
        $resp = $this->makeRequest(
            '/profile.json',
            HTTP_Request2::METHOD_PUT,
            $data
        );
        $data = $this->parseResponse($resp);
        if ($data !== null) {
            return true;
        }
        return false;
    }

    public function view()
    {
        $resp = $this->makeRequest('/profile.json');
        $data = $this->parseResponse($resp);
        return $data;
    }
}