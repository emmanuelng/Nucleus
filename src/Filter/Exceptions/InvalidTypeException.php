<?php

declare(strict_types=1);

namespace Nucleus\Filter\Exceptions;

class InvalidTypeException extends \Exception
{
    private $type;

    public function __construct($type)
    {
        parent::__construct("Invalid type $type.");
        $this->type = $type;
    }

    public function type()
    {
        return $this->type;
    }
}
