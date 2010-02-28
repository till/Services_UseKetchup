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

    // Meetings

    // Items

    // Notes

    // User Tests

    public function testIfWeCanCreateANewUser()
    {
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
        $useKetchup = new Services_UseKetchup($this->newUserToTestWith, 'paulrocks');

        $data           = new stdClass;
        $data->timezone = 'Dublin'; // Oh, noez. It's rainy there!

        $this->assertTrue($useKetchup->user->update($data));

        // $profile = $useKetchup->user->view();
        // $this->assertNotNull($profile);
        // $this->assertEquals($profile->timezone, 'Dublin');
    }
}
