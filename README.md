## Ohai! :)

Welcome to the PHP API wrapper for [useketchup.com][1].

[1]: http://useketchup.com

## Install?

We got two options, PEAR-based, and manual.

### PEAR (Yay, convenience!)

    pear install Services_UseKetchup-alpha

### Manual

 * clone the repository
 * add the path to your `include_path`
 * install the dependencies:
   * [HTTP_Request2][2]

[2]: http://pear.php.net/package/HTTP_Request2

## Usage

### Examples

Usage is simple:

    require_once 'Services/UseKetchup.php';
    $ketchup = new Services_UseKetchup('youremail', 'yourpassword');

    var_dump($ketchup->projects->show());
    var_dump($ketchup->meetings->show());

    // show items
    var_dump($ketchup->items->show($meeting_shortcode_url);

    // add a meeting
    $meeting        = new stdClass;
    $meeting->title = 'My new meeting';
    $ketchup->meetings->add($meeting);

    // get the last meeting we created, works too on items, notes
    $meeting = $ketchup->meetings->getLastCreated();

    $meeting->attendees = 'all my friends';
    $meeting->date      = 'tomorrow';

    // update it again
    $ketchup->meetings->update($meeting);

    // then use the $meeting object and add an $item
    $item        = new stdClass;
    $item->title = 'something for all my friends';

    $ketchup->items->add($meeting, $item);

..., examples are gonna be extended. Use the tests in the meantime!

### General

Try:

    $ketchup->users...
    $ketchup->notes...
    $ketchup->items...
    $ketchup->meetings...
    $ketchup->projects...