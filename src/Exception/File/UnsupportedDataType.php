<?php

declare(strict_types=1);

namespace App\Exception\File;

use Exception;

class UnsupportedDataType extends Exception
{
    protected $message = 'Unsupported data type';
}