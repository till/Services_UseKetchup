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

abstract class UseKetchupItemsTestCase extends UseKetchupTestCase
{
    // Items

    public function testItems()
    {
        $meeting        = new stdClass;
        $meeting->title = 'Item test!';

        $this->useKetchup->meetings->add($meeting);

        $meeting = $this->useKetchup->meetings->getLastCreated();

        $item          = new stdClass;
        $item->content = 'This be an item!';

        $this->assertTrue($this->useKetchup->items->add($meeting, $item));

        $item1 = $this->useKetchup->items->getLastCreated();

        $items = $this->useKetchup->items->show($meeting);

        $this->assertTrue(is_array($items));
        $this->assertNotEquals(0, count($items));

        $item2          = new stdClass;
        $item2->content = 'another one';

        $this->useKetchup->items->add($meeting, $item2);
        $item2 = $this->useKetchup->items->getLastCreated();

        $sort = new stdClass();
        $sort->items = array($item2->shortcode_url, $item1->shortcode_url);

        $this->assertTrue($this->useKetchup->items->sort($meeting, $sort));

        $this->assertTrue($this->useKetchup->items->delete($item1));
        $this->assertTrue($this->useKetchup->items->delete($item2));

        $this->useKetchup->meetings->delete($meeting);
    }
}
