<?php
class Services_UseKetchup_Notes extends Services_UseKetchup_Common
{
    protected $lastCreated;

    public function add($item, $note)
    {
        $id = $this->guessId($item);

        $data = json_encode($note);
        $resp = $this->makeRequest(
            "/items/{$id}/notes.json",
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

    public function delete($note)
    {
        $id = $this->guessId($note);

        $resp = $this->makeRequest(
            "/notes/{$id}.json",
            HTTP_Request2::METHOD_DELETE
        );
        $data = $this->parseResponse($resp);
        if ($data === 'Note Deleted Successfully') {
            return true;
        }
        return false;
    }

    public function show($item)
    {
        $id = $this->guessId($item);

        $resp = $this->makeRequest("/items/{$id}/notes.json");
        $data = $this->parseResponse($resp);
        return $data;
    }

    public function sort($item, $sort)
    {
        $id = $this->guessId($item);

        $data = json_encode($sort);
        $resp = $this->makeRequest(
            "/items/{$id}/sort_notes.json",
            HTTP_Request2::METHOD_PUT,
            $data
        );
        $data = $this->parseResponse($resp);
        if ($data !== null) {
            return true;
        }
        return false;
    }

    public function update($note)
    {
        $id = $note->shortcode_url;

        $data = json_encode($note);
        $resp = $this->makeRequest(
            "/notes/{$id}.json",
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