<?php

declare(strict_types=1);

namespace Laminas\ConfigAggregatorParameters;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException as SymfonyParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

use function array_walk_recursive;
use function is_array;
use function is_numeric;

/**
 * @template TParameters of array<string,mixed>
 * @psalm-type ProcessedConfig=array<string,mixed>&array{parameters:array<string,mixed>}
 */
class ParameterPostProcessor
{
    /**
     * @var array<string,mixed>
     * @psalm-var TParameters
     */
    private $parameters;

    /**
     * @param array<string,mixed> $parameters
     * @psalm-param TParameters $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @template TConfig of array<string,mixed>
     * @param array<string,mixed> $config
     * @return array<string,mixed>
     * @psalm-return ProcessedConfig
     */
    public function __invoke(array $config): array
    {
        try {
            $parameters = $this->getResolvedParameters();

            /** @psalm-suppress MissingClosureParamType */
            array_walk_recursive($config, static function (&$value) use ($parameters) {
                /** @psalm-suppress MixedAssignment */
                $value = $parameters->unescapeValue($parameters->resolveValue($value));
            });
        } catch (SymfonyParameterNotFoundException $exception) {
            throw ParameterNotFoundException::fromException($exception);
        }

        /** @var array<string,mixed> $allParameters */
        $allParameters        = $parameters->all();
        $config['parameters'] = $allParameters;

        /** @psalm-var ProcessedConfig $config */
        return $config;
    }

    private function resolveNestedParameters(array $values, string $prefix = ''): array
    {
        $convertedValues = [];
        /** @psalm-suppress MixedAssignment */
        foreach ($values as $key => $value) {
            // Do not provide numeric keys as single parameter
            if (is_numeric($key)) {
                continue;
            }

            /** @psalm-suppress MixedAssignment */
            $convertedValues[$prefix . $key] = $value;
            if (is_array($value)) {
                $convertedValues += $this->resolveNestedParameters($value, $prefix . $key . '.');
            }
        }

        return $convertedValues;
    }

    private function getResolvedParameters(): ParameterBag
    {
        $resolved = $this->resolveNestedParameters($this->parameters);
        $bag      = new ParameterBag($resolved);

        $bag->resolve();
        return $bag;
    }
}
