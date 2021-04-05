<?php
declare(strict_types=1);

namespace Laminas\ConfigAggregatorParameters;

final class LazyParameterPostProcessor
{
    /**
     * @var callable
     */
    private $parameterProvider;

    /**
     * @psalm-param callable():array<array-key, mixed> $parameterProvider
     */
    public function __construct(callable $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * @param array<array-key, mixed> $config
     *
     * @return array<array-key, mixed>
     */
    public function __invoke(array $config): array
    {

        $parameterProvider = $this->parameterProvider;

        return (new ParameterPostProcessor((array) $parameterProvider()))($config);
    }
}
