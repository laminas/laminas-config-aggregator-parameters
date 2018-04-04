<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator-parameters for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-config-aggregator-parameters/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\ConfigAggregatorParameters;

use PHPUnit\Framework\TestCase;
use Zend\ConfigAggregatorParameters\ParameterNotFoundException;
use Zend\ConfigAggregatorParameters\ParameterPostProcessor;

class ParameterPostProcessorTest extends TestCase
{
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
        $this->expectException(ParameterNotFoundException::class);
        $processor = new ParameterPostProcessor([]);
        $processor(['foo' => '%foo%']);
    }

    public function parameterProvider()
    {
        return [
            [
                // Root scope parameter
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
            [
                // Multi level parameter
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
}
