<?php

/**
 * @see       https://github.com/laminas/laminas-config-aggregator-parameters for the canonical source repository
 * @copyright https://github.com/laminas/laminas-config-aggregator-parameters/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-config-aggregator-parameters/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ConfigAggregatorParameters;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Laminas\ConfigAggregatorParameters\ParameterNotFoundException;
use Laminas\ConfigAggregatorParameters\ParameterPostProcessor;
use PHPUnit\Framework\TestCase;

class ParameterPostProcessorTest extends TestCase
{
    use ArraySubsetAsserts;

    public function parameterProvider()
    {
        return [
            'root-scoped-parameter' => [
                [
                    'foo' => 'bar',
                ],
                [
                    'config' => [
                        'param' => '%foo%',
                        'used_parameter' => '%%foo%%',
                    ],
                ],
                [
                    'config' => [
                        'param' => 'bar',
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
                        'param' => '%foo.bar%',
                        'used_parameter' => '%%foo.bar%%',
                    ],
                ],
                [
                    'config' => [
                        'param' => 'baz',
                        'used_parameter' => '%foo.bar%',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider parameterProvider
     */
    public function testCanApplyParameters(array $parameters, array $configuration, array $expected)
    {
        $processor = new ParameterPostProcessor($parameters);
        $processed = $processor($configuration);

        $this->assertArraySubset($expected, $processed);
    }

    public function testCanDetectMissingParameter()
    {
        $processor = new ParameterPostProcessor([]);
        $this->expectException(ParameterNotFoundException::class);
        $processor(['foo' => '%foo%']);
    }

    public function testResolvesParameterizedParameters()
    {
        $processor = new ParameterPostProcessor([
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => '%foo%',
            'nested' => [
                'foo' => '%bar%',
                'bar' => '%nested.foo%',
            ],
        ]);

        $processed = $processor(['foo' => '%nested.bar%']);

        $this->assertArraySubset([
            'foo' => 'baz',
            'parameters' => [
                'nested' => [
                    'bar' => 'baz',
                ],
            ],
        ], $processed);
    }

    public function testResolvesParameterizedParametersInReversedOrder()
    {
        $processor = new ParameterPostProcessor([
            'foo' => '%bar%',
            'bar' => '%baz%',
            'baz' => 'qux',
        ]);

        $processed = $processor([]);

        $this->assertArraySubset([
            'parameters' => [
                'foo' => 'qux',
            ],
        ], $processed);
    }
}
