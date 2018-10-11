<?php

namespace DesInventar\Legacy\Model;

use PHPUnit\Framework\TestCase;

final class StatusTest extends TestCase
{
    public function testStatus()
    {
        $status = new Status();
        $this->assertFalse($status->hasError());
        $this->assertFalse($status->hasWarning());

        $status->addError(100, 'Error Message');
        $this->assertTrue($status->hasError());
        $this->assertFalse($status->hasWarning());

        $status->clear();
        $this->assertFalse($status->hasError());
        $this->assertFalse($status->hasWarning());

        $status->addWarning(200, 'Warning Message');
        $this->assertFalse($status->hasError());
        $this->assertTrue($status->hasWarning());

        $status->clear();
        $status->addError(100, 'Error Message');
        $status->addWarning(200, 'Warning Message');
        $this->assertTrue($status->hasError());
        $this->assertTrue($status->hasWarning());
    }
}
