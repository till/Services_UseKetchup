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
 * Services_UseKetchup_Common
 *
 * @category Services
 * @package  Services_UseKetchup
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_UseKetchup
 */
abstract class Services_UseKetchup_Common
{
    /**
     * @var string $password The password to your useketchup.com account.
     * @see Services_UseKetchup::__construct()
     * @see self::setPassword()
     */
    protected $password;

    /**
     * @var string $username The username/email to your useketchup.com account.
     * @see Services_UseKetchup::__construct()
     * @see self::setUsername()
     */
    protected $username;

    /**
     * @var string $apiToken Returned from useketchup.com.
     * @see Services_UseKetchup::getApiToken()
     */
    protected $apiToken;

    /**
     * @var HTTP_Request2 $client The client to talk to the API.
     * @see self::makeRequest()
     */
    protected $client;

    /**
     * @var string $endpoint The API endpoint.
     */
    protected $endpoint = 'http://useketchup.com/api/v1';

    /**
     * Acceptor pattern.
     *
     * @param mixed $var
     *
     * @return void
     */
    public function accept($var)
    {
        if ($var instanceof HTTP_Request2) {
            $this->client = $var;
        }
        throw new InvalidArgumentException("Unknown: " . gettype($var));
    }

    /**
     * Collect debug information on the last call.
     *
     * @return array
     * @uses   self::$client
     */
    public function debugCall()
    {
        return array(
            'event'  => $this->client->getLastEvent(),
            'url'    => (string) $this->client->getUrl(),
            'data'   => $this->client->getBody(),
            'method' => $this->client->getMethod(),
        );
    }

    /**
     * Set an API Token.
     *
     * @return $this
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
        return $this;
    }

    /**
     * Set password.
     *
     * @param string $password Password of your useketchup.com account.
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Set username.
     *
     * @param string $username Username/email of your useketchup.com account.
     *
     * @return $this 
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Try to guess the ID (aka shortcode_url) from the variable.
     *
     * @param mixed $var Either stdClass or a string.
     *
     * @return string
     * @throws InvalidArgumentException When the stdClass has no shortcode_url.
     */
    protected function guessId($var)
    {
        if ($var instanceof stdClass) {
            if (!isset($var->shortcode_url)) {
                throw new InvalidArgumentException("Object must have attribute shortcode_url.");
            }
            return $var->shortcode_url;
        }
        return $var;
    }

    /**
     * Make an API request. Override the instance of HTTP_Request2 with
     * {@link self::accept()} if you need to configure the object with a proxy or
     * something similar.
     *
     * @param string $url    The URL to request agains.
     * @param string $method The request method ('GET', 'POST', 'PUT', etc.).
     * @param mixed  $data   Optional, most likely a json encoded string.
     *
     * @return HTTP_Request2_Response
     * @throws HTTP_Request2_Exception In case something goes wrong. ;)
     * @uses   self::apiToken()
     * @uses   self::$client
     * @see    self::accept()
     */
    protected function makeRequest($url, $method = HTTP_Request2::METHOD_GET, $data = null)
    {
        if ($this->apiToken !== null) {
            $url .= '?u=' . $this->apiToken;
        }

        if (!($this->client instanceof HTTP_Request2)) {
            $this->client = new HTTP_Request2;
        }

        $this->client
            ->setHeader('Content-Type: application/json')
            ->setAuth($this->username, $this->password)
            ->setMethod($method)
            ->setUrl($this->endpoint . $url);

        if ($data !== null) {
            $this->client->setBody($data);
        }

        $resp = $this->client->send();

        return $resp;
    }

    /**
     * Parse the response (from {@link self::makeRequest()}.
     *
     * @param HTTP_Request2_Response $resp The response returned from the API.
     *
     * @return mixed
     * @throws RuntimeException In case the API returned an error.
     */
    protected function parseResponse(HTTP_Request2_Response $resp)
    {
        $body = $resp->getBody();

        $headers = $resp->getHeader();
        if (isset($headers['content-type'])) {
            if ($headers['content-type'] == 'text/calendar; charset=utf-8') {
                return $body;
            }
        }

        switch ($body) {
        case 'Access Denied':
            throw new RuntimeException("API response: {$body}");
        case 'Item Deleted Successfully':
        case 'Meeting Deleted Successfully':
        case 'Note Deleted Successfully':
            return $body;
        default:
            $resp = json_decode($body);
            if (isset($resp->status)) {
                switch ($resp->status) {
                case 'internal_server_error':
                    throw new RuntimeException($resp->message);
                }
            }
            return $resp;
        }
    }
}