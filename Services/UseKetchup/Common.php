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
    protected $password;
    protected $username;

    protected $apiToken;

    protected $client;

    protected $endpoint = 'http://useketchup.com/api/v1';

    public function debugCall()
    {
        return array(
            'event'  => $this->client->getLastEvent(),
            'url'    => (string) $this->client->getUrl(),
            'data'   => $this->client->getBody(),
            'method' => $this->client->getMethod(),
        );
    }

    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    protected function guessId($var)
    {
        if ($var instanceof stdClass) {
            return $var->shortcode_url;
        }
        return $var;
    }

    protected function makeRequest($url, $method = HTTP_Request2::METHOD_GET, $data = null)
    {
        if ($this->apiToken !== null) {
            $url .= '?u=' . $this->apiToken;
        }

        $req = $this->client = new HTTP_Request2;
        $req
            ->setHeader('Content-Type: application/json')
            ->setAuth($this->username, $this->password)
            ->setMethod($method)
            ->setUrl($this->endpoint . $url);

        if ($data !== null) {
            $req->setBody($data);
        }

        $resp = $req->send();

        return $resp;
    }

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