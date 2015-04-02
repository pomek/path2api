<?php

namespace Pomek\Path2API;

use Pomek\Path2API\Contract\ReflectionMethodInterface;

class PhpDocParser
{

    /**
     * @var ReflectionMethodInterface
     */
    protected $reflection;

    public function __construct(ReflectionMethodInterface $reflection)
    {
        $this->reflection = $reflection;
    }

    /**
     * Returns an array with the names of exceptions that a method throws.
     *
     * @return array
     */
    public function getThrows()
    {
        $regexp = '/@throws\ {1,}(.*)/i';
        $matches = [];

        if (false == preg_match($regexp, $this->reflection->getDocComment(), $matches)) {
            return [];
        }

        return array_map(function ($item) {
            return trim($item);
        }, explode('|', $matches[1]));
    }

    /**
     * Return description of method.
     *
     * @return null|string
     */
    public function getDescription()
    {
        $lines_docs = explode("\n", $this->reflection->getDocComment());

        // Remove first line (/**)
        array_shift($lines_docs);

        // Remove last line (*/)
        array_pop($lines_docs);

        $lines_docs = array_map(function ($item) {
            return ltrim($item, '* ');
        }, $lines_docs);

        $lines_docs = array_filter($lines_docs, function ($item) {
            return !preg_match('/\ ?@[^s]+/i', $item);
        });

        return trim(join("\n", $lines_docs));
    }


}
