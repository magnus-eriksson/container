<?php

namespace Maer\Container;

class Binding
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var string
     */
    protected string $abstract;


    /**
     * @param ContainerInterface $container
     * @param string $abstract
     */
    public function __construct(ContainerInterface $container, string $abstract)
    {
        $this->container = $container;
        $this->abstract = $abstract;
    }


    /**
     * Give the binding an alias
     *
     * @param string $alias
     *
     * @return self
     */
    public function alias(string $alias): self
    {
        $this->container->alias($this->abstract, $alias);
        return $this;
    }


    /**
     * Set if the binding should be shared (reuse the same instance) or not
     *
     * @param bool $shared
     *
     * @return self
     */
    public function share(bool $shared = true): self
    {
        $this->container->share($this->abstract, $shared);
        return $this;
    }
}
