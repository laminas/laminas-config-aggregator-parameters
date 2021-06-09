<?php

declare(strict_types=1);

namespace LaminasTest\ConfigAggregatorParameters;

use Laminas\ConfigAggregatorParameters\LazyParameterPostProcessor;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class LazyParameterPostProcessorTest extends TestCase
{
    public function testParameterProviderNotCalledDuringInstantiation(): void
    {
        $callable = static function (): array {
            throw new RuntimeException('Parameter provider must not be called during instantiation!');
        };

        /** @psalm-suppress MixedArgumentTypeCoercion */
        new LazyParameterPostProcessor($callable);

        self::assertTrue(true);
    }

    public function testParametersAreBeingProcessed(): void
    {
        $provider = static function (): array {
            return ['foo' => 'bar'];
        };

        $config = [
            'foo' => '%foo%',
        ];

        $processor = new LazyParameterPostProcessor($provider);
        $processed = $processor($config);

        self::assertEquals(['foo' => 'bar', 'parameters' => ['foo' => 'bar']], $processed);
    }
}
