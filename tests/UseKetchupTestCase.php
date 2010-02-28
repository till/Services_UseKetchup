<?php
require_once dirname(__FILE__) . '/../Services/UseKetchup.php';

class UseKetchupTestCase extends PHPUnit_Framework_TestCase
{
    protected $config;
    protected $newUserToTestWith;
    protected $useKetchup;

    public function setUp()
    {
        $conf = dirname(__FILE__) . '/config.ini';
        if (!file_exists($conf)) {
            $this->markTestIncomplete("You need a config.ini file.");
        }
        $this->config = parse_ini_file($conf);

        $this->useKetchup = new Services_UseKetchup(
            $this->config['username'],
            $this->config['password']);

        $this->newUserToTestWith = 'till+' . mktime() . '@example.org';
    }

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

    // Meetings

    public function testMeetings()
    {
        
    }

    // Items

    // Notes

    // User Tests

    public function testIfWeCanCreateANewUser()
    {
        //$this->markTestIncomplete("I seem to be hitting a limit.");

        $user           = new stdClass;
        $user->email    = $this->newUserToTestWith;
        $user->password = 'paulrocks';
        $user->timezone = 'Berlin';

        $this->assertTrue($this->useKetchup->user->add($user));
    }

    public function testIfWeCanViewCurrentUser()
    {
        $profile = $this->useKetchup->user->view();
        $this->assertNotNull($profile);
        $this->assertEquals($profile->email, $this->config['username']);
    }

    public function testIfWeCanUpdateTheUser()
    {
        $this->markTestIncomplete("Doesn't seem to allow me to login with this user.");

        $useKetchup = new Services_UseKetchup($this->newUserToTestWith, 'paulrocks');

        $data           = new stdClass;
        $data->timezone = 'Dublin'; // Oh, noez. It's rainy there!

        $this->assertTrue($useKetchup->user->update($data));

        // $profile = $useKetchup->user->view();
        // $this->assertNotNull($profile);
        // $this->assertEquals($profile->timezone, 'Dublin');
    }
}
