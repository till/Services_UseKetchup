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

    public function delete($meeting)
    {
        $id = $this->guessId($meeting);

        $resp = $this->makeRequest(
            "/meetings/{$id}.json",
            HTTP_Request2::METHOD_DELETE
        );
        $data = $this->parseResponse($resp);
        if ($data === 'Meeting Deleted Successfully') {
            return true;
        }
        return false;
    }

    public function ics($lean = false)
    {
        return $this->show($lean, true);
    }

    public function previous($lean = false)
    {
        return $this->show($lean, false, '/meetings/previous.');
    }

    public function show($lean = false, $ics = false, $url = '/meetings.')
    {
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

    public function todays($lean = false)
    {
        return $this->show($lean, false, '/meetings/todays.');
    }

    public function upcoming($lean = false)
    {
        return $this->show($lean, false, '/meetings/upcoming.');
    }

    public function update($meeting)
    {
        $id = $meeting->shortcode_url;

        $data = json_encode($meeting);
        $resp = $this->makeRequest(
            "/meetings/{$id}.json",
            HTTP_Request2::METHOD_PUT,
            $data
        );
        $data = $this->parseResponse($resp);
        if ($data !== null) {
            return true;
        }
        return false;
    }

    public function getLastCreated()
    {
        if ($this->lastCreated === null) {
            throw new RuntimeException("You need to call add() first.");
        }
        return $this->lastCreated;
    }
}