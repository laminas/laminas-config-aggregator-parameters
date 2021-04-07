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
     * @psalm-param callable():array<string,mixed> $parameterProvider
     */
    public function __construct(callable $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * @param array<string,mixed> $config
     *
     * @return array<string,mixed>
     */
    public function __invoke(array $config): array
    {

        $parameterProvider = $this->parameterProvider;

        return (new ParameterPostProcessor((array) $parameterProvider()))($config);
    }
}
