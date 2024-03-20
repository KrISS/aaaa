<?php

function array_to_html($data)
{
    if (is_string($data)) {
        return $data;
    }

    $arrayAttributes = static fn(array $attrs): mixed => array_reduce(
        array_keys($attrs),
        static fn(?string $carry, mixed $key): string => $carry.' '.(is_int($key)
        ? $attrs[$key]
        : $key.'="'.$attrs[$key].'"')
    );

    return array_reduce(
        array_keys($data),
        static function (?string $carry, mixed $item) use ($data, $arrayAttributes) : ?string {
            if ('@' === $item) {
                return $carry;
            }

            if (is_int($item)) {
                return $carry.array_to_html($data[$item]);
            }

            $isSelfClose = false;
            $carry .= '<'.$item;
            if (is_array($data[$item])) {
                if (isset($data[$item]['@'])) {
                    $carry .= $arrayAttributes($data[$item]['@']);
                    $isSelfClose = (1 === count($data[$item]));
                } else {
                    $isSelfClose = ([] === $data[$item]);
                }
            }

            $carry .= '>';
            $carry .= array_to_html($data[$item]);
            if (!$isSelfClose) {
                $carry .= '</'.$item.'>';
            }

            return $carry;
        }
    );
}
