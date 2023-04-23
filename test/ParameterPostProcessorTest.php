<?php

declare(strict_types=1);

namespace LaminasTest\ConfigAggregatorParameters;

use Laminas\ConfigAggregatorParameters\ParameterNotFoundException;
use Laminas\ConfigAggregatorParameters\ParameterPostProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParameterPostProcessorTest extends TestCase
{
    /**
     * @psalm-return array<
     *     non-empty-string,
     *     array{
     *      0: array<string,mixed>,
     *      1: array<string,mixed>,
     *      2: array<string,mixed>
     *     }
     * >
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function parameterProvider(): array
    {
        return [
            'root-scoped-parameter' => [
                [
                    'foo' => 'bar',
                ],
                [
                    'config' => [
                        'param'          => '%foo%',
                        'used_parameter' => '%%foo%%',
                    ],
                ],
                [
                    'config' => [
                        'param'          => 'bar',
                        'used_parameter' => '%foo%',
                    ],
                ],
            ],
            'multi-level-parameter' => [
                [
                    'foo' => [
                        'bar' => 'baz',
                    ],
                ],
                [
                    'config' => [
                        'param'          => '%foo.bar%',
                        'used_parameter' => '%%foo.bar%%',
                    ],
                ],
                [
                    'config' => [
                        'param'          => 'baz',
                        'used_parameter' => '%foo.bar%',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string,mixed> $parameters
     * @param array<string,mixed> $configuration
     * @param array<string,mixed> $expected
     */
    #[DataProvider('parameterProvider')]
    public function testCanApplyParameters(array $parameters, array $configuration, array $expected): void
    {
        $processor = new ParameterPostProcessor($parameters);
        $processed = $processor($configuration);

        self::assertArrayHasKey('parameters', $processed);
        unset($processed['parameters']);

        self::assertEquals($expected, $processed);
    }

    public function testCanDetectMissingParameter(): void
    {
        $processor = new ParameterPostProcessor([]);
        $this->expectException(ParameterNotFoundException::class);
        $processor(['foo' => '%foo%']);
    }

    public function testResolvesParameterizedParameters(): void
    {
        $processor = new ParameterPostProcessor([
            'foo'    => 'bar',
            'bar'    => 'baz',
            'baz'    => '%foo%',
            'nested' => [
                'foo' => '%bar%',
                'bar' => '%nested.foo%',
            ],
        ]);

        $processed = $processor(['foo' => '%nested.bar%']);

        self::assertEquals([
            'foo'        => 'baz',
            'parameters' => [
                'foo'        => 'bar',
                'bar'        => 'baz',
                'baz'        => 'bar',
                'nested.foo' => 'baz',
                'nested.bar' => 'baz',
                'nested'     => [
                    'foo' => 'baz',
                    'bar' => 'baz',
                ],
            ],
        ], $processed);
    }

    public function testResolvesParameterizedParametersInReversedOrder(): void
    {
        $processor = new ParameterPostProcessor([
            'foo' => '%bar%',
            'bar' => '%baz%',
            'baz' => 'qux',
        ]);

        $processed = $processor([]);

        self::assertEquals([
            'parameters' => [
                'foo' => 'qux',
                'bar' => 'qux',
                'baz' => 'qux',
            ],
        ], $processed);
    }

    public function testProcessedConfigContainsParameterTypesafety(): void
    {
        $parameters = [
            'bool'   => true,
            'string' => 'lorem ipsum',
            'array'  => ['foo' => 'bar'],
            'int'    => 123,
            'float'  => 1.01,
        ];

        $processor = new ParameterPostProcessor($parameters);
        $processed = $processor([
            'bool'   => '%bool%',
            'string' => '%string%',
            'array'  => '%array%',
            'int'    => '%int%',
            'float'  => '%float%',
        ]);

        unset($processed['parameters']);
        self::assertEquals([
            'bool'   => true,
            'string' => 'lorem ipsum',
            'array'  => ['foo' => 'bar'],
            'int'    => 123,
            'float'  => 1.01,
        ], $processed);
    }

    public function testResolvedParametersApplyTypesafety(): void
    {
        $parameters = [
            'bool'   => true,
            'string' => 'lorem ipsum',
            'array'  => ['foo' => 'bar'],
            'int'    => 123,
            'float'  => 1.01,
        ];

        $processed = (new ParameterPostProcessor(
            $parameters + [
                'nested' => [
                    'bool'   => '%bool%',
                    'string' => '%string%',
                    'array'  => '%array%',
                    'int'    => '%int%',
                    'float'  => '%float%',
                ],
            ]
        ))([]);

        $processedParameters = $processed['parameters'];
        /** @var array<string,mixed> $processedNestedParameters */
        $processedNestedParameters = $processedParameters['nested'];
        unset($processedParameters['nested']);

        /** @psalm-suppress MixedAssignment */
        foreach ($processedNestedParameters as $parameter => $parameterValue) {
            $originalValue = $parameters[$parameter];
            self::assertEquals($originalValue, $parameterValue);
        }
    }
}
