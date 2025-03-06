<?php

namespace App\Tests\Unit\Utils;

use App\Utils\StringUtils;
use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase
{
    public function testGetEmailDomainFromAddress(): void
    {
        $this->assertEquals('', StringUtils::getEmailDomainFromAddress(''));
        $this->assertEquals('abc', StringUtils::getEmailDomainFromAddress('@abc'));
        $this->assertEquals('example', StringUtils::getEmailDomainFromAddress('test@example'));
        $this->assertEquals('example.com', StringUtils::getEmailDomainFromAddress('test@example.com'));
        $this->assertEquals('example.com.org', StringUtils::getEmailDomainFromAddress('test@example.com.org'));
        $this->assertEquals('domain.com', StringUtils::getEmailDomainFromAddress('test@example.com@domain.com'));
    }
}
