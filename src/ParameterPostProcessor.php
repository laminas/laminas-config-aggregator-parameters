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
     * @var array
     */
    private $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function __invoke(array $config) : array
    {

        try {
            $parameters = $this->getResolvedParameters();

            array_walk_recursive($config, function (&$value) use ($parameters) {
                $value = $parameters->unescapeValue($parameters->resolveValue($value));
            });
        } catch (SymfonyParameterNotFoundException $exception) {
            throw ParameterNotFoundException::fromException($exception);
        }

        $config['parameters'] = $parameters->all();

        return $config;
    }

    private function resolveNestedParameters(array $values, string $prefix = '') : array
    {
        $convertedValues = [];
        foreach ($values as $key => $value) {
            // Do not provide numeric keys as single parameter
            if (is_numeric($key)) {
                continue;
            }

            $convertedValues[$prefix . $key] = $value;
            if (is_array($value)) {
                $convertedValues += $this->resolveNestedParameters($value, $prefix . $key . '.');
            }
        }

        return $convertedValues;
    }

    private function getResolvedParameters() : ParameterBag
    {
        $resolved = $this->resolveNestedParameters($this->parameters);
        $bag = new ParameterBag($resolved);

        $bag->resolve();
        return $bag;
    }
}
