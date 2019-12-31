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
    /**
     * @var string
     */
    private $key;

    public static function fromException(BaseException $e) : self
    {
        $toReturn = new self(sprintf(
            'Found key "%s" within configuration, but it has no associated parameter defined',
            $e->getKey()
        ), $e->getCode(), $e);
        $toReturn->key = $e->getKey();
        return $toReturn;
    }

    public function getKey() : string
    {
        return $this->key;
    }
}
