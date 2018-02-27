<?php

namespace ZendTest\ConfigAggregatorParameters;

use PHPUnit\Framework\TestCase;
use Zend\ConfigAggregatorParameters\ParameterNotFoundException;
use Zend\ConfigAggregatorParameters\ParameterPostprocessor;

class ParameterPosthandlerTest extends TestCase
{

    /**
     * @dataProvider parameterProvider
     */
    public function testCanApplyParameters(array $parameters, array $configuration, array $expected)
    {
        $processor = new ParameterPostprocessor($parameters);
        $processed = $processor($configuration);

        $this->assertArraySubset($expected, $processed);
    }

    public function testCanDetectMissingParameter()
    {
        $this->expectException(ParameterNotFoundException::class);
        $processor = new ParameterPostprocessor([]);
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
