<?php
class Services_UseKetchup_Projects extends Services_UseKetchup_Common
{
    public function show($lean = false)
    {
        $resp = $this->makeRequest('/projects.json');
        $data = $this->parseResponse($resp);
        return $data;
    }

    public function update($project)
    {
        $id = $project->id;
        unset($project->id);

        $data = json_encode($project);
        $resp = $this->makeRequest(
            "/projects/{$id}.json",
            HTTP_Request2::METHOD_PUT,
            $data
        );
        $data = $this->parseResponse($resp);
        var_dump($data);
    }
}