<?php

namespace spec\Pomek\Path2API;

use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GenerateDocsConsoleSpec extends ObjectBehavior
{

    function let(Router $router, RouteCollection $collection)
    {
        $router->getRoutes()->willReturn($collection);

        $route1 = new Route('GET', '/api/', ['controller' => 'Stubs\Pomek\Path2API\Controller@homepage']);
        $route2 = new Route('POST', '/api/contact/{email}', ['controller' => 'Stubs\Pomek\Path2API\Controller@contact']);
        $route3 = new Route(['GET', 'POST'], '/api/test', function ($randomId) {
            return $randomId;
        });

        // Collection of routes
        $collection->getIterator()->willReturn(new \ArrayIterator([
            $route1,
            $route2,
            $route3
        ]));

        $this->beConstructedWith($router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pomek\Path2API\GenerateDocsConsole');
        $this->shouldHaveType('Illuminate\Console\Command');
    }

    function it_should_return_parsed_routes()
    {
        $this->getRoutesWithDocs()->shouldReturnDocs([]);
    }

    function getMatchers() {
        return [
            'docs' => function () {
                var_dump(func_get_args());
                return true;
            },
        ];
    }

}
