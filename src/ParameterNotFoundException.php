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
     * @var string
     */
    private $key;

    public function __construct(string $key, string $message)
    {
        $this->key = $key;
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
