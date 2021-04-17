<?php

/**
 * @see       https://github.com/laminas/laminas-config-aggregator-parameters for the canonical source repository
 * @copyright https://github.com/laminas/laminas-config-aggregator-parameters/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-config-aggregator-parameters/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ConfigAggregatorParameters;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException as BaseException;

use function sprintf;

/** @psalm-suppress MissingConstructor */
class ParameterNotFoundException extends InvalidArgumentException
{
    /** @var string */
    private $key;

    public static function fromException(BaseException $exception): self
    {
        /** @var string $key */
        $key = $exception->getKey();

        /** @var int $code */
        $code = $exception->getCode();

        $newParameterNotFoundException = new self(
            sprintf(
                'Found key "%s" within configuration, but it has no associated parameter defined',
                $key
            ),
            $code,
            $exception
        );

        $newParameterNotFoundException->key = $key;

        return $newParameterNotFoundException;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
