<?php

function array_html_simplify($array)
{
    $isUnique = static fn ($array, $ref): bool => 1 === count(array_filter($array, static fn ($item): bool => is_array($item) && key($item) === $ref));

    return array_reduce(
        array_keys($array),
        static function ($carry, $item) use (&$array, $isUnique) {
            $key = $item;
            $value = $array[$item];
            if (is_int($key) && is_array($value) && 1 === count($value) && $isUnique($array, key($value))) {
                $value = array_html_simplify($array[$key][key($value)]);
                $key = key($array[$item]);
            }

            unset($array[$item]);
            $array[$key] = $value;

            return $array;
        }
    );
}
