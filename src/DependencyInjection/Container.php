<?php

namespace Framework\DependencyInjection;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $definitions = [];
    private array $singletons = [];

    public function set(string $id, callable $factory, bool $singleton = false): void
    {
        $this->definitions[$id] = ['factory' => $factory, 'singleton' => $singleton];
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Service not found");
        }

        if ($this->definitions[$id]['singleton']) {
            if (!isset($this->singletons[$id])) {
                $this->singletons[$id] = ($this->definitions[$id]['factory'])($this);
            }
            return $this->singletons[$id];
        }

        return ($this->definitions[$id]['factory'])($this);
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }
}
