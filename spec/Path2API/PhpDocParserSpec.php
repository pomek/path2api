<?php

namespace spec\Pomek\Path2API;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PhpDocParserSpec extends ObjectBehavior
{
    function let(\ReflectionMethod $reflection)
    {
        $this->beConstructedWith($reflection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pomek\Path2API\PhpDocParser');
    }

    function it_should_return_throws_list_from_phpdoc(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * @throws \Exception | \LogicException | \PDOException
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getThrows()->shouldReturn([
            '\Exception',
            '\LogicException',
            '\PDOException'
        ]);
    }

    function it_should_return_empty_array_from_phpdoc(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 *
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getThrows()->shouldReturn([]);
    }

    function it_should_return_description_of_method_from_one_line_of_phpdoc(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * It's a simple one line description.
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getDescription()->shouldReturn("It's a simple one line description.");
    }

    function it_should_return_description_of_method_from_two_lines_of_phpdoc(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * It's a simple one line description.
 * Oops! I forgot about second line!
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getDescription()->shouldReturn(join("\n", [
            "It's a simple one line description.",
            "Oops! I forgot about second line!",
        ]));
    }

    function it_should_return_description_of_method_from_three_lines_of_phpdoc(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * It's a simple one line description.
 * Oops! I forgot about second line!
 * Is this method should work with more lines?
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getDescription()->shouldReturn(join("\n", [
            "It's a simple one line description.",
            "Oops! I forgot about second line!",
            "Is this method should work with more lines?",
        ]));
    }

    function it_should_return_null_when_description_is_empty(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 *
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getDescription()->shouldReturn("");
    }

    function it_should_return_params_with_types_hinting(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * @param  int    \$userId
 * @param  int    \$categoryId
 * @param  string \$word
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getParams()->shouldReturn([
            '$userId' => [
                'int'
            ],
            '$categoryId' => [
                'int'
            ],
            '$word' => [
                'string'
            ],
        ]);
    }

    function it_should_return_params_without_types_hinting(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * @param \$userId
 * @param \$categoryId
 * @param \$word
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getParams()->shouldReturn([
            '$userId' => [
                'mixed'
            ],
            '$categoryId' => [
                'mixed'
            ],
            '$word' => [
                'mixed'
            ],
        ]);
    }

    function it_should_return_params_with_object_types_hinting(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * @param stdClass \$userId
 * @param Integer | Float \$categoryId
 * @param Collection \$word
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getParams()->shouldReturn([
            '$userId' => [
                'stdClass'
            ],
            '$categoryId' => [
                'Integer',
                'Float'
            ],
            '$word' => [
                'Collection'
            ],
        ]);
    }

    function it_should_return_params_with_object_types_hinting_based_on_property_field(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * @property stdClass \$userId
 * @property Integer | Float \$categoryId
 * @property Collection \$word
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getParams()->shouldReturn([
            '$userId' => [
                'stdClass'
            ],
            '$categoryId' => [
                'Integer',
                'Float'
            ],
            '$word' => [
                'Collection'
            ],
        ]);
    }

    function it_should_return_parsed_php_doc_elements(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * It's a simple one line description.
 * Oops! I forgot about second line!
 * Is this method should work with more lines?
 *
 * Be careful: I have more lines!
 * @see: http://php.net
 *
 * @param int \$userId
 * @param int \$categoryId
 * @param string \$word
 * @throws \InvalidArgumentException|\LogicException
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getDescription()->shouldReturn(join("\n", [
            "It's a simple one line description.",
            "Oops! I forgot about second line!",
            "Is this method should work with more lines?",
            "",
            "Be careful: I have more lines!",
            "@see: http://php.net",
        ]));

        $this->getParams()->shouldReturn([
            '$userId' => [
                'int'
            ],
            '$categoryId' => [
                'int'
            ],
            '$word' => [
                'string'
            ],
        ]);

        $this->getThrows()->shouldReturn([
            '\InvalidArgumentException',
            '\LogicException',
        ]);
    }

    function it_should_return_parsed_php_doc_elements_second_test(\ReflectionMethod $reflection)
    {
        $docblock = <<<DOCBLOCK
/**
 * Handle calls to missing methods on the controller.
 *
 * @param  array   \$parameters
 * @return mixed
 *
 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
 */
DOCBLOCK;

        $reflection->getDocComment()->willReturn($docblock);

        $this->getDescription()->shouldReturn('Handle calls to missing methods on the controller.');

        $this->getParams()->shouldReturn([
            '$parameters' => [
                'array'
            ],
        ]);

        $this->getThrows()->shouldReturn([
            '\Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
        ]);
    }


}
