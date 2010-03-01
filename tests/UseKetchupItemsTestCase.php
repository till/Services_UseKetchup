<?php
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
