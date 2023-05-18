<?php
// KrISS aaaa: a simple and smart (or stupid) application as associative array
// Copyleft (ɔ) - Tontof - http://tontof.net
// use KrISS aaaa at your own risk

declare(strict_types=1);

/**
 * Middleware callable
 *
 * @param callable|array $item corresponding to a callable function
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
        $item = function ($object = null) {
            return $object;
        };
    }
    return $item;
}

/**
 * Middleware
 *
 * @param array<string|array|callable> $functions List of functions to apply
 * @param callable|array               $core      Core function to call
 *
 * @return callable to apply to a parameter
 */
function middleware(array $functions, $core = null): callable
{
    return array_reduce(
        $functions,
        function ($next, $item) {
            return function ($object = null) use ($next, $item) {
                $item = middleware_callable($item);
                $function = new ReflectionFunction($item);
                if (count($function->getParameters()) === 2) {
                    return $item($object, $next);
                }
                return $item($next($object));
            };
        },
        middleware_callable($core)
    );
}
