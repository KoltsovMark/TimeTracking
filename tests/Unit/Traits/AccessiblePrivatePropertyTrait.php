<?php

declare(strict_types=1);

namespace App\Tests\Unit\Traits;

use ReflectionClass;

/**
 * Trait PartialMockedTrait.
 */
trait AccessiblePrivatePropertyTrait
{
    /**
     * @param mixed $value
     *
     * @throws \ReflectionException
     */
    protected function setPrivateProperty(object $object, string $class, string $name, $value)
    {
        $reflectionClass = new ReflectionClass($class);
        $property = $reflectionClass->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
