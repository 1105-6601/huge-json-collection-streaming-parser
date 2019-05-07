<?php

declare(strict_types=1);

namespace HugeJsonCollectionStreamingParser\Test;

use HugeJsonCollectionStreamingParser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testExample()
    {
        $filePath = __DIR__ . '/data/example.json';

        $parser = new Parser($filePath);

        $index   = 0;
        $expects = $this->getExpects();
        while ($parser->next()) {
            $item = $parser->current();
            $this->assertSame($expects[$index], $item);
            $index++;
        }
    }

    private function getExpects(): array
    {
        return [
            [],
            [
                'Above is empty object test. And number value.' => 0,
                'Basic key and string value.'                   => 'abc',
                'Basic key and complex string value.'           => 'Thi{s[ is c]om}pl,ex va\\lue.\\\\',
            ],
            [
                'Th\\is is, Co]mpl[ex s{tri}ng key.\\'                           => '',
                'Th\\is is, Co]mpl[ex s{tri}ng key\\ and nested collection.\\\\' => [
                    [],
                    [
                        'Above is empty object test. And empty string' => '',
                        'number'                                       => 0,
                        'string'                                       => 'value',
                    ],
                    [
                        'Nested array' => [
                            [
                                'number' => 0,
                                'string' => 'value',
                            ],
                            [
                                'number' => 1,
                                'string' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'Number array'   => [0, 1, 2, 3, 4],
                'Array in array' => [
                    ['1', '1', '1', '1', '1'],
                    ['1', '1', '1', '1', '1'],
                    ['1', '1', '1', '1', '1'],
                    ['1', '1', '1', '1', '1'],
                    ['1', '1', '1', '1', '1'],
                ],
            ]
        ];
    }
}
