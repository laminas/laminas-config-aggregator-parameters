<?php

declare(strict_types=1);

namespace LaminasTest\ConfigAggregatorParameters;

use Laminas\ConfigAggregatorParameters\ParameterNotFoundException;
use PHPUnit\Framework\TestCase;

class ParameterNotFoundExceptionTest extends TestCase
{
    public function testThatTheKeyCanBeRetrieved(): void
    {
        $e = new ParameterNotFoundException('message', 0, null, 'some-key');
        self::assertSame('some-key', $e->getKey());
    }
}
