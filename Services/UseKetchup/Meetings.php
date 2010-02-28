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
        if (isset($data->project_id) && is_int($data->project_id)) {
            $this->lastCreated = $data;
            return true;
        }
        return false;
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