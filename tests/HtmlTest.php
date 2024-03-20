<?php

require_once('./vendor/autoload.php');

require_once('./src/helpers/html/html.php');

use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase {
    private $array = [
        0 => [
            '!DOCTYPE' => [
                '@' => [
                    0 => 'html',
                ],
            ],
        ],
        1 => [
            'html' => [
                0 => [
                    'head' => [
                        0 => [
                            'meta' => [
                                '@' => [
                                    'charset' => 'utf-8',
                                ],
                            ],
                        ],
                        1 => [
                            'title' => [
                                0 => 'Title',
                            ],
                        ],
                    ],
                ],
                1 => [
                    'body' =>[
                        0 => 'body',
                    ],
                ],
            ],
        ],
    ];

    private $arraySimplified = [
        '!DOCTYPE' => [
            '@' => [
                0 => 'html',
            ],
        ],
        'html' => [
            'head' => [
                'meta' => [
                    '@' => [
                        'charset' => 'utf-8',
                    ],
                ],
                'title' => [
                    0 => 'Title',
                ],
            ],
            'body' => [
                0 => 'body',
            ],
        ],
    ];

    private $string = '
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Title</title>
  </head>
  <body>
    body
  </body>
</html>
';

    private function stripTabs($string) {
        $patterns = [
            '/>\s*/',
            '/\s*</',
        ];
        $replacements = [
            '>',
            '<',
        ];

        return preg_replace($patterns, $replacements, trim(str_replace(["\t", "\n", "\r"], '', $string)));
    }

    public function testHtmlArray()
    {
        $array = html_to_array($this->string);
        $this->assertEqualsCanonicalizing($array, $this->array);
    }    

    public function testArrayHtml()
    {
        $string = array_to_html($this->array);
        $this->assertSame($this->stripTabs($this->string), $string);
    }

    public function testArrayHtmlSimplify()
    {
        $array = html_to_array($this->string);
        $arraySimplified = array_html_simplify($array);
        $this->assertEqualsCanonicalizing($arraySimplified, $this->arraySimplified);

        $stringArray = array_to_html($array);
        $stringArraySimplified = array_to_html($arraySimplified);

        $this->assertSame($stringArray, $stringArraySimplified);
    }
}

