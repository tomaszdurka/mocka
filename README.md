Mocka
=====

Status
------
[![Travis Build](https://api.travis-ci.org/tomaszdurka/mocka.png)](https://travis-ci.org/tomaszdurka/mocka)
[![Coverage Status](https://coveralls.io/repos/tomaszdurka/mocka/badge.png)](https://coveralls.io/r/tomaszdurka/mocka)

Installation
------------

Mocka is registered as composer package on (packagist)[https://packagist.org/packages/tomaszdurka/mocka].
```
"tomaszdurka/mocka": "dev-master"
```

Library usage
-----

Basic library usage

```php
$parentClassName = 'Exception';
$class = new ClassMock('MockedException', $parentClassName);
$exception1 = $class->newInstance(['exception message as constructor argument']);
$exception2 = $class->newInstanceWithoutConstructor();
```

Mocking methods
```php
$class = new ClassMock('MockedException', 'Exception');
$class->mockMethod('getMessage');

// It's also possible to mock method only for generated object
$object = $class->newInstance('message');
$object->mockMethod('getMessage');

// It's possible to mock non-existent methods which will work once mocked
$class->mockMethod('foo');

// It's also possible to mock static methods
$class->mockStaticMethod('bar');
```

Modifying method behaviour
```php
// Each method returned by any above mock methods return MethodMock object which can be manipulated
$class = new ClassMock('MockedException', 'Exception');
$mockedMethod = $class->mockMethod('getMessage');

// Set closure which will be executed when mocked method is called
$class->mockMethod('getMessage')->set(function () {
    return 'modified message';
});

// Set numbered callbacks
$class->mockMethod('getMessage')
    ->set(function () {
        return 'default message';
    })
    ->at(0, function () {
        return 'first message';
    })
    ->at(2, function () {
        return 'third message';
    });

// There is also shortcut to make method return certain value
$class->mockMethod('getMessages')->set('default')->at(0, 'first message');

// To check how many times method has been called simply use mocked method object
$mockedMethod = $class->mockMethod('getMessage');
// call method...
echo $mockedMethod->getCallCount();
```

Mocking interfaces
```php
$countableClass = new ClassMock('Collection', null, ['Countable']);
```

Referring back to original method
```php
// It's a way to add extra behaviour to original method functionality
$class = new ClassMock('MockedException', 'Exception');
$object = $class->newInstanceWithoutConstructor();
$object->mockMethod('getMessage')->set(function() use ($object) {
    return 'prefix-' . $object->callOriginalMethod('getMessage', func_get_args());
});
```

Using with test framework like PHPUnit
--------------------------------------
```php

class TestCase extends \PHPUnit_Framework_TestCase {

    use \Mocka\MockaTrait;

    public function testFoo() {
        $countableExceptionClass = $this->mockClass('DateTime', ['Countable']);
        $dateTimeObject = $this->mockObject('DateTime', '29-12-1984');
    }

    public function testMethodAssertions() {
        $pdo = $this->mockClass('PDO')->newInstanceWithoutConstructor();
        $pdo->mockMethod('exec')->set(function ($statement) {
            $this->assertInstanceOf('PDOStatement', $statement);
        });
    }
}
```
