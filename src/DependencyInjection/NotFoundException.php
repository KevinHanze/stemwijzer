<?php

namespace Framework\DependencyInjection;

use Psr\Container\NotFoundExceptionInterface;

/**
 * An exception that is thrown if an entry cannot be found in the dependency injection container.
 */
class NotFoundException extends \InvalidArgumentException implements NotFoundExceptionInterface
{
}