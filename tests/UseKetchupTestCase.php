<?php
/**
 * +-----------------------------------------------------------------------+
 * | Copyright (c) 2010, Till Klampaeckel                                  |
 * | All rights reserved.                                                  |
 * |                                                                       |
 * | Redistribution and use in source and binary forms, with or without    |
 * | modification, are permitted provided that the following conditions    |
 * | are met:                                                              |
 * |                                                                       |
 * | o Redistributions of source code must retain the above copyright      |
 * |   notice, this list of conditions and the following disclaimer.       |
 * | o Redistributions in binary form must reproduce the above copyright   |
 * |   notice, this list of conditions and the following disclaimer in the |
 * |   documentation and/or other materials provided with the distribution.|
 * | o The names of the authors may not be used to endorse or promote      |
 * |   products derived from this software without specific prior written  |
 * |   permission.                                                         |
 * |                                                                       |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
 * | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
 * | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
 * | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
 * | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
 * | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
 * | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
 * | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
 * | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
 * |                                                                       |
 * +-----------------------------------------------------------------------+
 * | Author: Till Klampaeckel <till@php.net>                               |
 * +-----------------------------------------------------------------------+
 *
 * PHP version 5
 *
 * @category Testing 
 * @package  Services_UseKetchup
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  GIT: $Id$
 * @link     http://github.com/till/Services_UseKetchup
 */

/**
 * UseKetchupTestCase
 * @ignore
 */
require_once 'Services/UseKetchup.php';

/**
 * UseKetchupTestCase
 *
 * @category Services
 * @package  Services_UseKetchup
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_UseKetchup
 */
abstract class UseKetchupTestCase extends PHPUnit_Framework_TestCase
{
    protected $config;
    protected $newUserToTestWith;
    protected $useKetchup;
    protected $request;

    public function setUp()
    {
        $conf = dirname(__FILE__) . '/config.ini';
        if (!file_exists($conf)) {

            $this->markTestIncomplete("You need a config.ini file.");

            if (!(include 'Testing/GenerateMock.php')) {
                // prep
            }

            $this->useKetchup = new Services_UseKetchup('mock','mock');

        } else {
            $this->config = parse_ini_file($conf);

            $this->useKetchup = new Services_UseKetchup(
                $this->config['username'],
                $this->config['password']);
        }

        /**
         * Generate mocks. Only when we have a real user.
         */
        if (file_exists($conf)) {
            $this->request = new HTTP_Request2;
            $this->request->attach(new Testing_GenerateMock(dirname(__FILE__) . '/mock'));
            $this->useKetchup->accept($this->request);
        }

        $this->newUserToTestWith = 'till+' . mktime() . '@example.org';
    }

    public function tearDown()
    {
        unset($this->config);
        unset($this->useKetchup);
        unset($this->request);
    }
}
