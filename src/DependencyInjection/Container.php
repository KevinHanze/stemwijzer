<?php

namespace Framework\DependencyInjection;

use Psr\Container\ContainerInterface;

/**
 * Simple dependency injection container.
 *
 * Supports service registration and optional singleton behavior.
 */
class Container implements ContainerInterface
{
    private array $definitions = [];
    private array $singletons = [];

    /**
     * Registers a service with the container.
     *
     * @param string $id Identifier for the service (e.g. class name)
     * @param callable $factory Factory function that returns the service instance
     * @param bool $singleton If true, the service will be cached as a singleton
     */
    public function set(string $id, callable $factory, bool $singleton = false): void
    {
        $this->definitions[$id] = ['factory' => $factory, 'singleton' => $singleton];
    }

    /**
     * Returns an instance of the requested service.
     *
     * @param string $id
     * @return mixed
     * @throws NotFoundException If the service is not registered
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Service not found");
        }

        if ($this->definitions[$id]['singleton']) {
            // Return cached instance if already created
            if (!isset($this->singletons[$id])) {
                $this->singletons[$id] = ($this->definitions[$id]['factory'])($this);
            }
            return $this->singletons[$id];
        }

        // Always return a new instance for non-singletons
        return ($this->definitions[$id]['factory'])($this);
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }
}
