<?php
class Services_UseKetchup_Items extends Services_UseKetchup_Common
{
    protected $lastCreated;

    public function add($meeting, $item)
    {
        if ($meeting instanceof stdClass) {
            $id = $meeting->shortcode_url;
        } else {
            $id = $meeting;
        }

        $data = json_encode($item);
        $resp = $this->makeRequest(
            "/meetings/{$id}/items.json",
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

    public function delete($item)
    {
        if ($item instanceof stdClass) {
            $id = $item->shortcode_url;
        } else {
            $id = $item;
        }
        $resp = $this->makeRequest(
            "/items/{$id}.json",
            HTTP_Request2::METHOD_DELETE
        );
        $data = $this->parseResponse($resp);
        if ($data === 'Item Deleted Successfully') {
            return true;
        }
        return false;
    }

    public function show($meeting)
    {
        if ($meeting instanceof stdClass) {
            $id = $meeting->shortcode_url;
        } else {
            $id = $meeting;
        }

        $resp = $this->makeRequest("/meetings/{$id}/items.json");
        $data = $this->parseResponse($resp);
        return $data;
    }

    public function sort()
    {
        throw new Exception("Not implemented.");
    }

    public function update($item)
    {
        $id = $item->shortcode_url;

        $data = json_encode($meeting);
        $resp = $this->makeRequest(
            "/items/{$id}.json",
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