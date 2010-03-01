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
 * Services_UseKetchup_Items
 *
 * @category Services
 * @package  Services_UseKetchup
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_UseKetchup
 */
class Services_UseKetchup_Items extends Services_UseKetchup_Common
{
    /**
     * @var stdClass $lastCreated
     * @see self::add()
     * @see self::getLastCreated()
     */
    protected $lastCreated;

    /**
     * Add an item to the given meeting.
     *
     * @param mixed    $meeting Either stdClass (with shortcode_url), or the
     *                          shortcode_url of the item.
     * @param stdClass $item    The item object.
     *
     * @return boolean
     * @see    self::getLastCreated()
     */
    public function add($meeting, stdClass $item)
    {
        $id = $this->guessId($meeting);

        $data = json_encode($item);
        $resp = $this->makeRequest(
            "/meetings/{$id}/items.json",
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

    /**
     * Delete an item.
     *
     * @param mixed $item Either stdClass (with shortcode_url), or the
     *                    shortcode_url of the item.
     *
     * @return boolean
     */
    public function delete($item)
    {
        $id = $this->guessId($item);

        $resp = $this->makeRequest(
            "/items/{$id}.json",
            HTTP_Request2::METHOD_DELETE
        );
        $data = $this->parseResponse($resp);
        if ($data === 'Item Deleted Successfully') {
            return true;
        }
        return false;
    }

    /**
     * Show all items of a given meeting.
     *
     * @param mixed $meeting Either stdClass (with shortcode_url), or the
     *                       shortcode_url of the item.
     *
     * @return array An array stacked with stdClass.
     */
    public function show($meeting)
    {
        $id = $this->guessId($meeting);

        $resp = $this->makeRequest("/meetings/{$id}/items.json");
        $data = $this->parseResponse($resp);
        return $data;
    }

    /**
     * Change sort order of items on a meeting.
     *
     * @param mixed    $meeting Either stdClass (with shortcode_url), or the
     *                          shortcode_url of the item.
     * @param stdClass $sort    {'items':['SHORTCODE_URL', 'SHORTCODE_URL']}
     *
     * @return boolean
     */
    public function sort($meeting, stdClass $sort)
    {
        $id = $this->guessId($meeting);

        $data = json_encode($sort);
        $resp = $this->makeRequest(
            "/meetings/{$id}/sort_items.json",
            HTTP_Request2::METHOD_PUT,
            $data
        );
        $data = $this->parseResponse($resp);
        if ($data !== null) {
            return true;
        }
        return false;
    }

    /**
     * Update an item.
     * 
     * @param stdClass $item The item object.
     *
     * @return boolean
     */
    public function update(stdClass $item)
    {
        $id = $this->guessId($item);

        $data = json_encode($item);
        $resp = $this->makeRequest(
            "/items/{$id}.json",
            HTTP_Request2::METHOD_PUT,
            $data
        );
        $data = $this->parseResponse($resp);
        if ($data !== null) {
            return true;
        }
        return false;
    }

    /**
     * Get last created meeting.
     * 
     * @return stdClass
     * @uses   self::$lastCreated
     * @see    self::add()
     * @throws RuntimeException When called prior to add().
     */
    public function getLastCreated()
    {
        if ($this->lastCreated === null) {
            throw new RuntimeException("You need to call add() first.");
        }
        return $this->lastCreated;
    }
}