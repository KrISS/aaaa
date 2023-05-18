<?php 

require_once('./vendor/autoload.php');

require_once('./src/helpers/middleware/middleware.php');

use PHPUnit\Framework\TestCase;

function append($object) {
    $object[] = 'append';
    return $object;
}

function core($object) {
    $object[] = 'core';
    return $object;
}

function before($object, $next) {
    $object[] = 'before';
    return $next($object);
}

function after($object, $next) {
    $object = $next($object);
    $object[] = 'after';
    return $object;
}

function custom($value) {
    return function($object) use ($value) {
        $object[] = $value;
        return $object;        
    };
}

function test1($object) {
    return "test1";
}

function test2($object) {
    return "test2";
}

class MiddlewareTest extends TestCase {
    public function testMiddlewareAppend()
    {
        $middleware = middleware(['append', 'append']);
        $this->assertEqualsCanonicalizing($middleware(), ['append', 'append']);
        $this->assertEqualsCanonicalizing($middleware([]), ['append', 'append']);
        $this->assertEqualsCanonicalizing($middleware(['init']), ['init', 'append', 'append']);
    }
    
    public function testMiddlewareAppendWithCore()
    {
        $middleware = middleware(['append', 'append'], 'core');
        $this->assertEqualsCanonicalizing($middleware(), ['core', 'append', 'append']);
        $this->assertEqualsCanonicalizing($middleware([]), ['core', 'append', 'append']);
        $this->assertEqualsCanonicalizing($middleware(['init']), ['init', 'core', 'append', 'append']);
    }

    public function testMiddlewareNext()
    {
        $middleware = middleware(['before', 'after', 'before', 'after']);
        $this->assertEqualsCanonicalizing($middleware(), ['before', 'before', 'after', 'after']);
        $this->assertEqualsCanonicalizing($middleware([]), ['before', 'before', 'after', 'after']);
        $this->assertEqualsCanonicalizing($middleware(['init']), ['init', 'before', 'before', 'after', 'after']);
    }
    
    public function testMiddlewareNextWithCore()
    {
        $middleware = middleware(['before', 'after', 'before', 'after'], 'core');
        $this->assertEqualsCanonicalizing($middleware(), ['before', 'before', 'core', 'after', 'after']);
        $this->assertEqualsCanonicalizing($middleware([]), ['before', 'before', 'core', 'after', 'after']);
        $this->assertEqualsCanonicalizing($middleware(['init']), ['init', 'before', 'before', 'core', 'after', 'after']);
    }
    
    public function testMiddlewareWithArrayFunction()
    {
        $middleware = middleware(['append', ['custom', 'value'], 'append']);
        $this->assertEqualsCanonicalizing($middleware(), ['append', 'value', 'append']);
        $this->assertEqualsCanonicalizing($middleware([]), ['append', 'value', 'append']);
        $this->assertEqualsCanonicalizing($middleware(['init']), ['init', 'append', 'value', 'append']);
    }
    
    public function testMiddlewareWithArrayCoreFunction()
    {
        $middleware = middleware(['append', ['custom', 'value'], 'append'], ['custom', 'core']);
        $this->assertEqualsCanonicalizing($middleware(), ['core', 'append', 'value', 'append']);
        $this->assertEqualsCanonicalizing($middleware([]), ['core', 'append', 'value', 'append']);
        $this->assertEqualsCanonicalizing($middleware(['init']), ['init', 'core', 'append', 'value', 'append']);
    }
    
    public function testMiddlewareCompleteFunction()
    {
        $middleware = middleware(['append', 'after', 'before', ['custom', 'value'], 'after', 'before', 'append'], ['custom', 'core']);
        $this->assertEqualsCanonicalizing($middleware(), ['before', 'before', 'core', 'append', 'value', 'append', 'after', 'after']);
        $this->assertEqualsCanonicalizing($middleware([]), ['before', 'before', 'core', 'append', 'value', 'append', 'after', 'after']);
        $this->assertEqualsCanonicalizing($middleware(['init']), ['init', 'before', 'before', 'core', 'append', 'value', 'append', 'after', 'after']);
    }
    
    public function testMiddlewareSingleValue()
    {
        $middleware = middleware(['test1', 'test2']);
        $this->assertEqualsCanonicalizing($middleware(), "test2");
        $middleware = middleware(['test1', 'test2', 'test1']);
        $this->assertEqualsCanonicalizing($middleware(), "test1");
    }
}
