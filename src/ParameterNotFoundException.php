<?php

declare(strict_types=1);

namespace Laminas\ConfigAggregatorParameters;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException as BaseException;
use Throwable;

use function sprintf;

class ParameterNotFoundException extends InvalidArgumentException
{
    /** @var string */
    private $key;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, string $key = '')
    {
        parent::__construct($message, $code, $previous);
        $this->key = $key;
    }

    public static function fromException(BaseException $e): self
    {
        $code = (int) $e->getCode();
        $key  = (string) $e->getKey();
        return new self(sprintf(
            'Found key "%s" within configuration, but it has no associated parameter defined',
            $key
        ), $code, $e, $key);
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
