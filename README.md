PhpRedisBundle
==============

Symfony 2 Bundle for phpredis extension

!!!WARNING
==========
This bundle is under construction. It is highly recommended not using this bundle for production


WORKING METHODS
===============
* strings: get, set, setex, setnx
* keys: del, exists ,keys
* server: flushDB
* connection: close


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