<?php

function _add_start_tag(array $params): array
{
    $nodes = &$params['parentNodes'];
    $tag = [];
    $nodes[count($nodes) - 1][] = [
        $params['current'] => &$tag,
    ];
    $nodes[] = &$tag;
    $params['isClosingTag'] = in_array($params['current'], $params['selfClosingTags']);
    $params['current'] = '';

    return $params;
}

function _add_end_tag(array $params)
{
    array_pop($params['parentNodes']);
    $params['isClosingTag'] = false;
    $params['current'] = '';

    return $params;
}

function _add_attrs($params)
{
    $nodes = &$params['parentNodes'];
    if (!isset($nodes[count($nodes) - 1]['@'])) {
        $nodes[count($nodes) - 1]['@'] = [];
    }

    if (is_array($params['current'])) {
        $nodes[count($nodes) - 1]['@'] += $params['current'];
    } else {
        $nodes[count($nodes) - 1]['@'][] = $params['current'];
    }

    $params['current'] = '';

    return $params;
}

function _parse_attribute_value($params, string $item)
{
    switch ($item) {
        case '"':
            if (!empty($params['current'][key($params['current'])])) {
                $params = _add_attrs($params);
                $params['next'] = '_parse_attribute';
            }

            break;

        default:
            $params['current'][key($params['current'])] .= $item;
            break;
    }

    return $params;
}

function _parse_attribute($params, string $item)
{
    switch ($item) {
        case '=':
            $params['current'] = [
                $params['current'] => '',
            ];
            $params['next'] = '_parse_attribute_value';
            break;

        case ' ':
        case '>':
            if (!empty($params['current'])) {
                $params = _add_attrs($params);
            }

            if ($params['isClosingTag']) {
                $params = _add_end_tag($params);
            }

            $params['next'] = '_parse_content';
            break;

        default:
            $params['current'] .= $item;
            break;
    }

    return $params;
}

function _parse_tag($params, string $item)
{
    switch ($item) {
        case ' ':
            if (!$params['isClosingTag']) {
                $params = _add_start_tag($params);
            }

            $params['next'] = '_parse_attribute';
            break;

        case '>':
            if (!$params['isClosingTag']) {
                $params = _add_start_tag($params);
            }

            if ($params['isClosingTag']) {
                $params = _add_end_tag($params);
            }

            $params['next'] = '_parse_content';
            break;

        case '/':
            $params['isClosingTag'] = true;
            break;

        default:
            $params['current'] .= $item;
            break;
    }

    return $params;
}

function _parse_content(array $params, $item)
{
    switch ($item) {
        case '<':
            $nodes = &$params['parentNodes'];
            $content = trim((string) $params['current']);
            if ($content !== '' && $content !== '0') {
                $nodes[count($nodes) - 1][] = preg_replace("/[ \n]{2,}/", ' ', $content);
            }

            $params['current'] = '';
            $params['next'] = '_parse_tag';
            break;

        default:
            $params['current'] .= (PHP_EOL === $item) ? ' ' : $item;
            break;
    }

    return $params;
}

function html_to_array($string)
{
    $params = [
        'current' => '',
        'parentNodes' => [[]],
        'selfClosingTags' => ['!DOCTYPE', 'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'],
        'isClosingTag' => false,
        'next' => '_parse_content',
    ];

    return array_reduce(
        str_split((string) $string),
        static function ($carry, $item) use (&$params) {
            $params = call_user_func_array($params['next'], [$params, $item]);
            return current($params['parentNodes']);
        }
    );
}
