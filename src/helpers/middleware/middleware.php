<?php

// KrISS aaaa: a simple and smart (or stupid) application as associative array
// Copyleft (ɔ) - Tontof - http://tontof.net
// use KrISS aaaa at your own risk

declare(strict_types=1);

/**
 * Middleware callable.
 *
 * @param array|callable $item corresponding to a callable function
 *
 * @return callable corresponding to $item
 */
function middleware_callable($item): callable
{
    if (is_array($item)) {
        $fun = array_shift($item);
        if (is_callable($fun)) {
            $item = call_user_func_array($fun, $item);
        }
    }

    if (!is_callable($item)) {
        $item = static fn($obj = null) => $obj;
    }

    return $item;
}

/**
 * Middleware.
 *
 * @param array<array|callable|string> $functions List of functions to apply
 * @param array|callable               $core      Core function to call
 *
 * @return callable to apply to a parameter
 */
function middleware(array $functions, $core = null): callable
{
    return array_reduce(
        $functions,
        static fn($next, $item): \Closure => static function ($obj = null) use ($next, $item) {
            $item = middleware_callable($item);
            $function = new ReflectionFunction($item);
            return 2 === count($function->getParameters())
                ? $item($obj, $next)
                : $item($next($obj));
        },
        middleware_callable($core)
    );
}
