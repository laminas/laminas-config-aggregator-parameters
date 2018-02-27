<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator-parameters for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-config-aggregator-parameters/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ConfigAggregatorParameters;

class ParameterNotFoundException extends \InvalidArgumentException
{

    /**
     * @param string $parameter
     *
     * @return self
     */
    public static function fromMissingParameter($parameter): self
    {
        return new self(sprintf('Missing parameter %s within your parameter configuration.', $parameter));
    }
}
