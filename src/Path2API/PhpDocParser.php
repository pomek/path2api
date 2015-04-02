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
     * Returns description of method.
     *
     * @return string
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

    /**
     * Returns an array with parsed params.
     *
     * @return array
     */
    public function getParams()
    {
        $lines_docs = array_filter($this->getParsedDocs(), function($item) {
           return preg_match('/^@param/i', $item);
        });

        $params = [];

        $lines_docs = array_map(function ($item) {
            return preg_replace('/^@param (.*)/i', '$1', $item);
        }, $lines_docs);

        foreach ($lines_docs as $item) {
            $matches = [];

            if (!preg_match('/(.*)\ ?(\$.*)$/i', $item, $matches)) {
                continue;
            }

            if (empty($matches[1])) {
                $matches[1] = "mixed";
            }

            $params[$matches[2]] = array_map(function ($row) {
                return trim($row);
            }, explode('|', $matches[1]));
        }

        return $params;
    }

    /**
     * Returns an array with parsed lines from PHPDoc.
     *
     * @return array
     */
    protected function getParsedDocs()
    {
        $lines_docs = explode("\n", $this->reflection->getDocComment());

        // Remove first line (/**)
        array_shift($lines_docs);

        // Remove last line (*/)
        array_pop($lines_docs);

        return array_map(function ($item) {
            return ltrim($item, '* ');
        }, $lines_docs);
    }

}
