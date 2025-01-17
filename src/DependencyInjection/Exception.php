<?php

namespace Framework\DependencyInjection;

use Psr\Container\ContainerExceptionInterface;

/**
 * An exception that is thrown if an error occurs in the dependency injection container.
 */
class Exception extends \Exception implements ContainerExceptionInterface
{
}