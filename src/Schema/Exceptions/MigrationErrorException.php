<?php

declare(strict_types=1);

namespace Nucleus\Schema\Exceptions;

use Exception;

class MigrationErrorException extends Exception
{
    public function __construct(string $msg)
    {
        parent::__construct($msg);
    }
}