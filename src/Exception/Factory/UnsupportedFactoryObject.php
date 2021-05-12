<?php

declare(strict_types=1);

namespace App\Exception\Factory;

use Exception;

class UnsupportedFactoryObject extends Exception
{
    protected $message = 'Unsupported factory object';
}
