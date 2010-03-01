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
 * Services_UseKetchup_Projects
 *
 * @category Services
 * @package  Services_UseKetchup
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_UseKetchup
 */
class Services_UseKetchup_Projects extends Services_UseKetchup_Common
{
    /**
     * Show all projects.
     *
     * @param boolean $lean If set to 'true', we save resources and it'll include less data.
     *
     * @return array An array stacked with objects.
     * @todo   useketchup.com needs to implement ?lean=true
     */
    public function show(bool $lean = null)
    {
        $resp = $this->makeRequest('/projects.json');
        $data = $this->parseResponse($resp);
        return $data;
    }

    /**
     * Update a project.
     *
     * @param stdClass $project The project object.
     *
     * @return boolean
     */
    public function update(stdClass $project)
    {
        $id = $project->id;
        unset($project->id);

        $data = json_encode($project);
        $resp = $this->makeRequest(
            "/projects/{$id}.json",
            HTTP_Request2::METHOD_PUT,
            $data
        );
        $data = $this->parseResponse($resp);
        if ($data !== null) { 
            return true;
        }
        return false;
    }
}