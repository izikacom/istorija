# Available storage

* InMemoryDAO
* PredisDAO
* RedisDAO
* DoctrineDAO
* ElasticSearchDAO

# Use buffering system

```php
<?php

use \Dayuse\Istorija\DAO\Proxy\Buffer;
use \Dayuse\Istorija\DAO\Storage\InMemoryDAO;

$storage = new InMemoryDAO();
$buffer = new Buffer($storage);

$buffer->save('user-123', 'John Doe');
$buffer->save('user-456', 'Jane Doe');

$storage->find('user-123'); // null

$buffer->flushAndCommit();

$storage->find('user-123'); // John Doe
```

