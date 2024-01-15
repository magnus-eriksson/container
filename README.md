### Dependency Injection Container

This is a small and simple dependency injection container that implements the Psr/Container interface.
It also uses reflection to resolve dependencies for unbound services.

### Bind services

```php
$container = new Maer\Container\Container;

// Let the container resolve dependencies and instantiate the service
// This is basically the same as not binding it
$container->bind(\Foo\Bar\Hello::class);

// Bind a service with a callback that instantiate and returns the service
$container->bind(\Foo\Bar\Hello::class, function (Container $container) {
    return new \Foo\Bar\Hello();
});

// Bind a service that has already been instantiated
$hello = new \Foo\Bar\Hello();
$container->bind(\Foo\Bar\Hello::class, $hello);

// Bind a service as shared (it will return the same instance on each call)
$container->bind(...)->share(true);

// Bind a service and give it an alias
$container->bind(...)->alias('hello');
```


### Get services
```php
// Get a service from or throught the container
// - If it isn't bound, it will use reflection to get and resolve dependencies (recursively), if possible
$service = $container->get(\Foo\Bar\Hello::class);
```

