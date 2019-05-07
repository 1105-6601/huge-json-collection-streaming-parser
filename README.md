Huge JSON collection streaming parser for PHP
=============================

[![Build Status](https://travis-ci.org/1105-6601/huge-json-collection-streaming-parser.png?branch=master)](https://travis-ci.org/1105-6601/huge-json-collection-streaming-parser)
[![GitHub tag](https://img.shields.io/github/tag/1105-6601/huge-json-collection-streaming-parser.svg?label=latest)](https://packagist.org/packages/suemitsu/huge-json-collection-streaming-parser) 
[![Packagist](https://img.shields.io/packagist/dt/1105-6601/huge-json-collection-streaming-parser.svg)](https://packagist.org/packages/suemitsu/huge-json-collection-streaming-parser)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/packagist/l/1105-6601/huge-json-collection-streaming-parser.svg)](https://packagist.org/packages/suemitsu/huge-json-collection-streaming-parser)

This package is streaming parser for processing huge JSON documents.
Can load the objects of JSON documents which is an array of objects one by one.
The structure of JSON documents needs to be the following structure.

e.g.)

```json
[
  {
    "id": 1,
    "name": "foo",

    ...

  },
  {
    "id": 2,
    "name": "bar",

    ...

  },

  ...

]
```

This package is compliant with [PSR-4](http://www.php-fig.org/psr/4/), [PSR-1](http://www.php-fig.org/psr/1/), and
[PSR-2](http://www.php-fig.org/psr/2/).
If you notice compliance oversights, please send a patch via pull request.


Installation
-----

To install `HugeJsonCollectionStreamingParser` you can either clone this repository or you can use composer.

```
composer require suemitsu/huge-json-collection-streaming-parser
```


Usage
-----

```php
$filePath = 'path/to/huge-json-file.json';

$parser = new Parser($filePath);

while ($parser->next()) {
    $item = $parser->current();

    // do anything...
}
```

There is a complete example of this in `example/example.php`.


License
-------

[MIT License](http://mit-license.org/)
