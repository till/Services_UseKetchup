<?php
require_once dirname(__FILE__) . '/UseKetchupTestCase.php';

class UseKetchupNotesTestCase extends UseKetchupTestCase
{
    // Notes

    public function testNotes()
    {
        $meeting        = new stdClass;
        $meeting->title = "This is a test meeting for notes!";
        $this->useKetchup->meetings->add($meeting);

        $meeting = $this->useKetchup->meetings->getLastCreated();

        $item          = new stdClass;
        $item->content = 'This is an important item.';
        $this->useKetchup->items->add($meeting, $item);

        $item = $this->useKetchup->items->getLastCreated();

        $note1          = new stdClass;
        $note1->content = 'note #1';

        $note2          = new stdClass;
        $note2->content = 'note #2';

        $this->useKetchup->notes->add($item, $note1);
        $note1 = $this->useKetchup->notes->getLastCreated();

        $this->useKetchup->notes->add($item, $note2);
        $note2 = $this->useKetchup->notes->getLastCreated();

    }
}
