<?php
class Services_UseKetchup_Meetings extends Services_UseKetchup_Common
{
    protected $lastCreated;

    public function add($meeting)
    {
        $data = json_encode($meeting);
        $resp = $this->makeRequest(
            '/meetings.json',
            HTTP_Request2::METHOD_POST,
            $data
        );
        $data = $this->parseResponse($resp);
        if ($data !== null) {
            $this->lastCreated = $data;
            return true;
        }
        return false;
    }

    public function ics($lean = false)
    {
        return $this->show($lean, true);
    }

    public function show($lean = false, $ics = false)
    {
        $url = '/meetings.';
        if ($ics === false) {
            $url .= 'json';
        } else {
            $url .= 'ics';
        }
        if ($lean === true) {
            $url .= '?lean=true';
        }

        $resp = $this->makeRequest($url);
        $data = $this->parseResponse($resp);
        return $data;
    }

    public function update($meeting)
    {
        $data = json_encode($meeting);
        $resp = $this->makeRequest(
            '/meetings.json',
            HTTP_Request2::METHOD_PUT,
            $data
        );
        $data = $this->parseResponse($resp);
        var_dump($data);
    }

    public function getLastCreated()
    {
        if ($this->lastCreated === null) {
            throw new RuntimeException("You need to call add() first.");
        }
        return $this->lastCreated;
    }
}