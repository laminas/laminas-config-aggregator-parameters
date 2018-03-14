<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator-parameters for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-config-aggregator-parameters/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ConfigAggregatorParameters;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ParameterPostprocessor
{

    /**
     * @var ParameterBag
     */
    private $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = new ParameterBag($parameters);
    }

    public function __invoke(array $config): array
    {
        $parameters = $this->parameters;

        // First, convert values to needed parameters
        $convertedParameters = $this->convertValues($parameters->all());
        $parameters->clear();
        $parameters->add($convertedParameters);

        try {
            array_walk_recursive($config, function (&$value) use ($parameters) {
                $value = $parameters->unescapeValue($parameters->resolveValue($value));
            });
        } catch (\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException $exception) {
            throw new ParameterNotFoundException($exception->getKey(), $exception->getMessage());
        }

        $config['parameters'] = $parameters->all();

        return $config;
    }

    private function convertValues(array $values, string $prefix = ''): array
    {
        $convertedValues = [];
        foreach ($values as $key => $value) {
            // Do not provide numeric keys as single parameter
            if (is_numeric($key)) {
                continue;
            }

            $convertedValues[$prefix . $key] = $value;
            if (is_array($value)) {
                $convertedValues += $this->convertValues($value, $prefix . $key . '.');
            }
        }

        return $convertedValues;
    }
}
