<?php

/**
 * @see       https://github.com/laminas/laminas-config-aggregator-parameters for the canonical source repository
 * @copyright https://github.com/laminas/laminas-config-aggregator-parameters/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-config-aggregator-parameters/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ConfigAggregatorParameters;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException as SymfonyParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ParameterPostProcessor
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

    public function __invoke(array $config) : array
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
        } catch (SymfonyParameterNotFoundException $exception) {
            throw ParameterNotFoundException::fromException($exception);
        }

        $config['parameters'] = $parameters->all();

        return $config;
    }

    private function convertValues(array $values, string $prefix = '') : array
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
