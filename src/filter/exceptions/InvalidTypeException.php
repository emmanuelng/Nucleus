<?php

declare(strict_types=1);

namespace Nucleus\Filter\Exceptions;

class InvalidTypeException extends \Exception
{
    private $type;

    public function __construct(string $type)
    {
        parent::__construct("Invalid type $type.");
        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }
}
