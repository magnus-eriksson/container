<?php

namespace Maer\Container;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    protected array $bindings = [];

    /**
     * @var array
     */
    protected array $shared = [];

    /**
     * @var object[]
     */
    protected array $resolved = [];

    /**
     * @var array
     */
    protected array $aliases = [];


    /**
     * @inheritDoc
     */
    public function bind(string $abstract, object|callable|string|null $concrete = null): Binding
    {
        if ($this->has($abstract)) {
            unset($this[$abstract]);
        }

        if (is_object($concrete) && !$concrete instanceof \Closure) {
            $this->resolved[$abstract] = $concrete;
        }

        $this->bindings[$abstract] = $concrete ?? $abstract;

        return new Binding($this, $abstract);
    }


    /**
     * @inheritDoc
     */
    public function has(string $abstract): bool
    {
        return key_exists($abstract, $this->bindings)
            || $this->isResolved($abstract);
    }


    /**
     * @inheritDoc
     */
    public function get(string $abstract, array $parameters = []): mixed
    {
        $abstract = $this->aliases[$abstract] ?? $abstract;

        if ($this->isResolved($abstract)) {
            return $this->resolved[$abstract];
        }



        $reference = $this->bindings[$abstract] ?? $abstract;
        $concrete = $this->resolve($reference, $parameters);

        if ($this->isShared($abstract)) {
            $this->resolved[$abstract] = $concrete;
        }

        return $concrete;
    }


    /**
     * @inheritDoc
     */
    public function share(string $abstract, bool $shared = true): self
    {
        $this->shared[$abstract] = $shared;

        if (!$shared && $this->isShared($abstract)) {
            unset($this->shared[$abstract]);
        }

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function isShared(string $abstract): bool
    {
        return key_exists($abstract, $this->shared);
    }


    /**
     * @inheritDoc
     */
    public function alias(string $abstract, string $alias): self
    {
        $this->aliases[$alias] = $abstract;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function isAlias(string $abstract): bool
    {
        return key_exists($abstract, $this->aliases);
    }


    /**
     * @inheritDoc
     */
    public function isResolved(string $abstract): bool
    {
        return key_exists($abstract, $this->resolved);
    }


    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $abstract): bool
    {
        return $this->has($abstract);
    }


    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $abstract): mixed
    {
        return $this->get($abstract);
    }


    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $abstract, mixed $concrete): void
    {
        $this->bind((string)$abstract, $concrete);
    }


    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $abstract): void
    {
        $abstract = (string)$abstract;

        if ($this->has($abstract)) {
            unset($this->bindings[$abstract]);
        }

        if ($this->isShared($abstract)) {
            unset($this->shared[$abstract]);
        }

        if ($this->isResolved($abstract)) {
            unset($this->resolved[$abstract]);
        }
    }


    /**
     * Resolve dependencies (first from the container, secondary using reflection) and g
     *
     * @param callable|string $concrete
     *
     * @return mixed
     */
    protected function resolve(callable|string $concrete, array $parameters = []): mixed
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }

        if (is_object($concrete)) {
            return $concrete;
        }

        $class = new \ReflectionClass($concrete);

        $params = [];
        if ($constructor = $class->getConstructor()) {
            $params = $this->getMethodParameters($constructor, $parameters);
        }

        if ($params === false) {
            throw new \InvalidArgumentException("Missing constructor arguments");
        }

        return $class->newInstance(...array_replace($params, $parameters));
    }


    /**
     * Get list of resolvable dependencies
     *
     * @param \ReflectionMethod $method
     * @param array $parameters
     *
     * @return array|false If any dependency cannot be resolved through the container, false is returned
     */
    protected function getMethodParameters(\ReflectionMethod $method, array $parameters = []): array|false
    {
        foreach ($method->getParameters() as $param) {
            if (key_exists($param->getName(), $parameters) || $param->isDefaultValueAvailable()) {
                continue;
            }

            $type = $this->getParameterType($param);

            if ($type === null) {
                return false;
            }

            $parameters[$param->getName()] = $this->get($type);
        }

        return $parameters;
    }


    /**
     * @param \ReflectionParameter $parameter
     *
     * @return string|null
     */
    protected function getParameterType(\ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();

        return $type instanceof \ReflectionNamedType && !$type->isBuiltin()
            ? $type->getName()
            : null;
    }
}
