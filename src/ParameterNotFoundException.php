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

class ParameterNotFoundException extends InvalidArgumentException
{
    public static function fromException(BaseException $exception) : self
    {
        /** @var string $key */
        $key = $exception->getKey();
        /** @var int $code */
        $code = $exception->getCode();

        return new self(sprintf(
            'Found key "%s" within configuration, but it has no associated parameter defined',
            $key
        ),
            $code,
            $exception
        );
    }
}
