# Ulrack Invocation Extension - Create an invocation

After the package has been installated, it can be used to invoke a method on a
service and pass the result back to another service. Using the following example
class:
```php
<?php

namespace MyVendor\MyPackage\MyService;

use GrizzIt\Cache\Common\CacheInterface;

class MyClass
{
    /**
     * Contains the cache for this class.
     *
     * @var CacheInterface
     */
    private $cache;

    /**
     * Constructor.
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
}
```

This has the following service declaration:
```json
{
    "my.service": {
        "class": "\\MyVendor\\MyPackage\\MyService\\MyClass",
        "parameters": {
            "cache": "@{invocations.cache.my-class}"
        }
    }
}
```

Every Ulrack project has the cache manager available as a core service. This can
be used to create the cache instance. Without having to create a separate service
or directly using the cache manager.

The invocations declaration would then look like the following example:
```json
{
    "cache.my-class": {
        "service": "services.core.cache.manager",
        "method": "getCache",
        "parameters": {
            "key": "my-class"
        }
    }
}
```

This declaration will retrieve the cache manager and return the result of
invoking `getCache` on the cache manager with the `key` parameter value of
`my-class`. In essence this will create a cache instance with a key related to
the implementation.

When `my.service` is used anywhere in the application, the invocation is
performed to construct the class.

## Further reading

[Back to usage index](index.md)

[Installation](installation.md)