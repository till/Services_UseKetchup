<?php
require_once dirname(__FILE__) . '/UseKetchupTestCase.php';

class UseKetchupProjectsTestCase extends UseKetchupTestCase
{
    // General

    public function testIfWeCanGetAnAPIKey()
    {
        $this->assertNotNull($this->useKetchup->getApiToken());
    }

    // Projects

    public function testProjects()
    {
        $meeting               = new stdClass;
        $meeting->title        = 'This is a test meeting!';
        $meeting->project_name = 'Services_UseKetchup-Test-Project-' . mktime();

        $this->assertTrue($this->useKetchup->meetings->add($meeting));

        $lastCreated = $this->useKetchup->meetings->getLastCreated();
        $this->assertEquals($meeting->project_name, $lastCreated->project_name);

        $projects = $this->useKetchup->projects->show();

        $totalProjects = count($projects);
        $this->assertTrue(is_array($projects));
        $this->assertNotNull($projects);
        $this->assertTrue(is_array($projects));

        $project       = new stdClass;
        $project->id   = $projects[$totalProjects-1]->shortcode_url;
        $project->name = "This is an updated project name";

        $this->assertTrue($this->useKetchup->projects->update($project));

        $this->assertTrue(($projects[0] instanceof stdClass));
    }
}
