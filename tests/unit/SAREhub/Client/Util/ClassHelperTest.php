<?php

namespace SAREhub\Client\Util;

use PHPUnit\Framework\TestCase;

class ClassHelperTest extends TestCase
{

    public function testGetShortName()
    {
        $this->assertEquals('ClassHelperTest', ClassHelper::getShortName(ClassHelperTest::class));
    }
}
