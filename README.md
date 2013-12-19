PhpRedisBundle
==============

Symfony 2 Bundle for phpredis extension

!!!WARNING
==========
This bundle is under construction. It is highly recommended not using this bundle for production


WORKING METHODS
===============
* strings: append, bitCount, decr, get, getBit, getRange, getSet, incr, incrByFloat, mget, mset, set, setBit, setex, setnx, setRange, strlen
* keys: del, exists ,keys
* server: flushDB
* connection: close

Methods Ignore
==============
* strings: delete

Methods not working:
=================
* strings: bitOp


Method Informations
===================
* set: parameter timeout is defined as float in docbloc and function. It is an int or long. Float will rase an error
* bitOf: always returns 0

Configuration
=============
here is a first sample configuration
**config.yml**
```
php_redis:
    clients:
        default:
            host: localhost
            port: ~
            db: 0
            pconnect: true
            logging: true
            connection_timeout: 1
        importstatus:
            host: localhost
            port: ~
            db: 1
            pconnect: true
            logging: true
```



Testing within Symfony2
=======================

for running the unit and integration tests add test parameters to your config:
**config_test.yml**
```
parameters:
  redis:
    host: localhost
    port: 6379
    db: 10
```

Please keep in mind, that you have to run your own redis server.