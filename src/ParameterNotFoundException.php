<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator-parameters for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-config-aggregator-parameters/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ConfigAggregatorParameters;

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
