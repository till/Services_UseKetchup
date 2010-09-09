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
 * @category Services
 * @package  Services_UseKetchup
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  GIT: $Id$
 * @link     http://github.com/till/Services_UseKetchup
 */

/**
 * @ignore
 */
require_once 'HTTP/Request2.php';

/**
 * Services_UseKetchup_Common
 */
require_once 'Services/UseKetchup/Common.php';

/**
 * Services_UseKetchup
 *
 * @category Services
 * @package  Services_UseKetchup
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_UseKetchup
 */
class Services_UseKetchup extends Services_UseKetchup_Common
{
    /**
     * @var array $subs See self::__get()
     */
    protected $subs = array();

    /**
     * __construct
     *
     * Initialize the class, and get an API token.
     *
     * @param string $username The useketchup.com username/email.
     * @param string $password The useketchup.com password.
     *
     * @return $this
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Magic getter.
     *
     * @param string $sub Items, Meetings, Notes, Projects or User.
     *
     * @return mixed Services_UseKetchup_*
     * @throws LogicException In case something unsupported is requested.
     * @uses   self::getApiToken()
     */
    public function __get($sub)
    {
        $this->apiToken = $this->getApiToken();

        $sub = ucwords(strtolower($sub));
        switch ($sub) {
        case 'Items':
        case 'Meetings':
        case 'Notes':
        case 'Projects':
        case 'User':

            if (!isset($this->subs[$sub])) {

                $className = "Services_UseKetchup_{$sub}";


                if (!class_exists($className)) {
                    $fileName  = str_replace('_', '/', $className) . '.php';

                    if (!(include $fileName)) {
                        throw new RuntimeException("Cannot find: $fileName.");
                    }
                }

                $this->subs[$sub] = new $className;
                $this->subs[$sub]
                    ->setApiToken($this->apiToken)
                    ->setPassword($this->password)
                    ->setUsername($this->username);
            }
            return $this->subs[$sub];

            break;

        default:
            throw new LogicException("Unknown $sub. Maybe not implemented?");
        }
    }

    /**
     * Return/request an API token from useketchup. If one is already set, we return
     * it instead of requesting one again.
     *
     * @return string
     * @uses   self::$apiToken
     */
    public function getApiToken()
    {
        if ($this->apiToken !== null) {
            return $this->apiToken;
        }
        $resp = $this->makeRequest('/profile.json');
        $data = $this->parseResponse($resp);

        $this->apiToken = $data->single_access_token;

        return $this->apiToken;
    }

    /**
     * To be able to mock requests, etc. later/
     *
     * @param string $token Required for each call.
     *
     * @retrun $this
     */
    public function setApiToken($token)
    {
        $this->apiToken = $token;
        return $this;
    }
}