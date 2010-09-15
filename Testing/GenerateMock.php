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
 * @package  Testing_GenerateMock
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
 * Testing_GenerateMock
 *
 * This turn's the obvserver example for HTTP_Request2 into hopefully something to
 * use to mock out HTTP APIs.
 *
 * @category Testing
 * @package  Testing_GenerateMock
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_UseKetchup
 */
class Testing_GenerateMock implements SplObserver
{
    /**
     * @var string $dir
     * @see self::__construct()
     * @see self::update()
     */
    protected $dir;

    /**
     * @var resource $fp
     * @see self::update()
     */
    protected $fp;

    /**
     * CTOR
     *
     * @param string $dir The directory to save mocks to.
     *
     * @return $this
     * @throws Testing_GenerateMock_Exception If $dir is invalid.
     */
    public function __construct($dir)
    {
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new Testing_GenerateMock_Exception("'{$dir}' is not a directory.");
        }
        $this->dir = $dir;
    }

    /**
     * Observer for the update event.
     *
     * The object is to create files and save the response to:
     * request:  GET /foobar
     * filename: GET_foobar
     *
     * See {@link self::getFilename()} for the filename.
     * 
     * @param SplSubject $subject
     *
     * @return void
     * @uses   self::getFilename()
     * @uses   self::$dir
     * @uses   self::$fp
     */
    public function update(SplSubject $subject)
    {
        $event = $subject->getLastEvent();

        switch ($event['name']) {
        case 'receivedHeaders':

            $filename = $this->getFilename($subject);

            $target = $this->dir . DIRECTORY_SEPARATOR . $filename;
            if (!($this->fp = @fopen($target, 'wb'))) {
                throw new Testing_GenerateMock_Exception("Cannot open target file '{$target}'");
            }
            break;

        case 'receivedBodyPart':
        case 'receivedEncodedBodyPart':
            if (!is_resource($this->fp)) {
                throw new Testing_GenerateMock_Exception("Invalid file handle.");
            }
            fwrite($this->fp, $event['data']);
            break;

        case 'receivedBody':
            if (is_resource($this->fp)) {
                fclose($this->fp);
            }
            break;
        }
    }

    /**
     * Generate a filename from two parameters.
     *
     * @param Net_URL2 $url
     * @param string   $method
     *
     * @return string
     */
    public static function generateFilename(Net_URL2 $url, $method)
    {
        $filename    = $method . str_replace('/', '_', $url->getPath());
        $queryString = $url->getQuery();
        if ($queryString !== false && !empty($queryString)) {
            $filename .= "-" . str_replace('&', '_and_', $queryString);
        }

        return $filename;
    }

    /**
     * Generate a filename.
     *
     * 1) The idea is that when the response contains an attachment, we use that name.
     * 2) If not, we create a filename from METHOD-URL-QUERYSTRING
     *
     * @param SplSubject $subject
     *
     * @return string
     * @uses   self::generateFilename()
     * @see    self::update()
     */
    protected function getFilename(SplSubject $subject)
    {
        $event = $subject->getLastEvent();

        if ($disposition = $event['data']->getHeader('content-disposition')
                && 0 == strpos($disposition, 'attachment')
                && preg_match('/filename="([^"]+)"/', $disposition, $m)
        ) {
            $filename = basename($m[1]);
            return $filename;
        }

        $filename    = self::generateFilename($subject->getUrl(), $subject->getMethod());

        return $filename;
    }
}