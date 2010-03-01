<?php
require_once dirname(__FILE__) . '/UseKetchupTestCase.php';

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
