# LiteOnion

## Installation

```bash
composer require avonnadozie/liteonion
```

## Usage

```php
class BeforeLayer implements LayerInterface {

    public function peel($object, Closure $next)
    {
        $object->runs[] = 'before';

        return $next($object);
    }

}

class AfterLayer implements LayerInterface {

    public function peel($object, Closure $next)
    {
        $response = $next($object);

        $object->runs[] = 'after';

        return $response;
    }

}

$object = new StdClass;
$object->runs = [];

$onion = new Onion;
$end = $onion->layer([
                new AfterLayer(),
                new BeforeLayer(),
                new AfterLayer(),
                new BeforeLayer()
            ])
            ->peel($object, function($object){
                $object->runs[] = 'core';
                return $object;
            });

var_dump($end);
```

Will output

```
..object(stdClass)#161 (1) {
  ["runs"]=>
  array(5) {
    [0]=>
    string(6) "before"
    [1]=>
    string(6) "before"
    [2]=>
    string(4) "core"
    [3]=>
    string(5) "after"
    [4]=>
    string(5) "after"
  }
}
```
