<?php

namespace Maer\Container;

use ArrayAccess;
use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface, ArrayAccess
{
    /**
     * Bind a service to the container
     *
     * @param string $abstract
     * @param object|callable|string|null|null $concrete
     *
     * @return Binding
     */
    public function bind(string $abstract, object|callable|string|null $concrete = null): Binding;


    /**
     * Check if a service has been bound to the container
     *
     * @param string $id
     *
     * @return bool
     */
    public function has(string $abstract): bool;


    /**
     * Get a service from/through the container
     * - If a service isn't bound, it will use reflection to resolve any dependencies
     *
     * @param string $abstract
     * @param array $arguments
     *
     * @return mixed
     */
    public function get(string $abstract, array $arguments = []): mixed;


    /**
     * Set if a service should reuse the same instance or create new
     *
     * @param string $abstract
     * @param bool $shared
     *
     * @return self
     */
    public function share(string $abstract, bool $shared = true): self;


    /**
     * Check if a service is shared
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function isShared(string $abstract): bool;


    /**
     * Add an alias to a service
     *
     * @param string $abstract
     * @param string $alias
     *
     * @return self
     */
    public function alias(string $abstract, string $alias): self;


    /**
     * Check if the abstract is an alias
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function isAlias(string $abstract): bool;


    /**
     * Check if a binding has already been resolved
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function isResolved(string $abstract): bool;
}
