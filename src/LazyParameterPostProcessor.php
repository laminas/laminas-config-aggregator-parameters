<?php

declare(strict_types=1);

namespace Laminas\ConfigAggregatorParameters;

/**
 * @template TParameters of array<string,mixed>
 * @psalm-import-type ProcessedConfig from ParameterPostProcessor
 */
final class LazyParameterPostProcessor
{
    /**
     * @var callable():array<string,mixed>
     * @psalm-var callable():TParameters
     */
    private $parameterProvider;

    /**
     * @param callable():array<string,mixed> $parameterProvider
     * @psalm-param callable():TParameters $parameterProvider
     */
    public function __construct(callable $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * @param array<string,mixed> $config
     * @return array<string,mixed>
     * @psalm-return ProcessedConfig
     */
    public function __invoke(array $config): array
    {
        $parameterProvider = $this->parameterProvider;

        return (new ParameterPostProcessor($parameterProvider()))($config);
    }
}
