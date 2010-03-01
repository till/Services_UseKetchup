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
require_once dirname(__FILE__) . '/UseKetchupTestCase.php';

/**
 * UseKetchupMeetingsTestCase 
 *
 * @category Services
 * @package  Services_UseKetchup
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://github.com/till/Services_UseKetchup
 */
class UseKetchupMeetingsTestCase extends UseKetchupTestCase
{
    // Meetings

    public function testCreateMeeting()
    {
        $meeting            = new stdClass;
        $meeting->title     = 'ohai';
        $meeting->location  = "Your mom's house";
        $meeting->attendees = 'Till';
        $meeting->date      = 'yesterday';

        $this->assertTrue($this->useKetchup->meetings->add($meeting));
    }

    public function testUpdateDeleteMeeting()
    {
        $meeting            = new stdClass;
        $meeting->title     = 'ohai2';
        $meeting->attendees = 'Paul';

        $this->useKetchup->meetings->add($meeting);

        $meeting = $this->useKetchup->meetings->getLastCreated();
        $meeting->attendees = 'Paul,Till';

        $this->assertTrue($this->useKetchup->meetings->update($meeting));

        // delete it :)
        $this->assertTrue($this->useKetchup->meetings->delete($meeting));
        
    }

    public function testShowMeetings()
    {
        $meetings = $this->useKetchup->meetings->show();
        $this->assertNotEquals(0, count($meetings));

        $this->assertTrue(($meetings[0] instanceof stdClass));

        $ics = $this->useKetchup->meetings->ics(false);
        $this->assertContains('PRODID://Hyper Tiny//Ketchup//EN', $ics);
    }

    public function testPrevious()
    {
        $previous = $this->useKetchup->meetings->previous(true);
        $this->assertTrue(is_array($previous));
    }

    public function testUpcoming()
    {
        $upcoming = $this->useKetchup->meetings->upcoming(true);
        $this->assertTrue(is_array($upcoming));
    }

    public function testTodays()
    {
        $todays = $this->useKetchup->meetings->todays(true);
        $this->assertTrue(is_array($todays));
        $this->assertNotNull($todays);
    }
}
