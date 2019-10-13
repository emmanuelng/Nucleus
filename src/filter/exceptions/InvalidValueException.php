<?php

declare(strict_types=1);

namespace Nucleus\Filter\Exceptions;

class InvalidValueException extends \Exception
{
    private $type;
    private $value;

    public function __construct(string $type, $value)
    {
        parent::__construct("Invalid value.");
        $this->type = $type;
        $this->value = $value;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function value()
    {
        return $this->value;
    }
}
