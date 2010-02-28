<?php
class Services_UseKetchup_Items extends Services_UseKetchup_Common
{
    protected $lastCreated;

    public function add($meeting, $item)
    {
        $id = $this->guessId($meeting);

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
        $id = $this->guessId($item);

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
        $id = $this->guessId($meeting);

        $resp = $this->makeRequest("/meetings/{$id}/items.json");
        $data = $this->parseResponse($resp);
        return $data;
    }

    public function sort($meeting, $sort)
    {
        $id = $this->guessId($meeting);

        $data = json_encode($sort);
        $resp = $this->makeRequest(
            "/meetings/{$id}/sort_items.json",
            HTTP_Request2::METHOD_PUT,
            $data
        );
        $data = $this->parseResponse($resp);
        if ($data !== null) {
            return true;
        }
        return false;
    }

    public function update($item)
    {
        $id = $item->shortcode_url;

        $data = json_encode($item);
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