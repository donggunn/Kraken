<?php

namespace spec\SevenShores\Kraken;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContactSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SevenShores\Kraken\Contact');
    }
}
