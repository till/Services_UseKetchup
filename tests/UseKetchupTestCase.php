<?php
require_once 'Services/UseKetchup.php';

abstract class UseKetchupTestCase extends PHPUnit_Framework_TestCase
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

    public function tearDown()
    {
        unset($this->config);
        unset($this->useKetchup);
    }
}
