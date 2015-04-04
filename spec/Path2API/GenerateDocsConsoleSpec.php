<?php

namespace spec\Pomek\Path2API;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GenerateDocsConsoleSpec extends ObjectBehavior
{

    function let(Repository $config)
    {
        $config->get('path2api')->willReturn(include __DIR__ . '/../../src/config/path2api.php');
        $router = new Router(new Dispatcher);

        $router->get('/api', ['uses' => 'Stubs\Pomek\Path2API\Controller@homepage']);
        $router->post('/api/contact/{email}', ['uses' => 'Stubs\Pomek\Path2API\Controller@contact']);
        $router->match(['GET', 'POST'], '/api/test/{id}', function ($id) {
            return $id;
        });

        $this->beConstructedWith($router, $config);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pomek\Path2API\GenerateDocsConsole');
        $this->shouldHaveType('Illuminate\Console\Command');
    }

    function it_should_return_parsed_routes()
    {
        $this->getRoutesWithDocs()->shouldReturnDocs([
            [
                'action' => 'Stubs\Pomek\Path2API\Controller@homepage',
                'description' => 'It\'s a simple controller.',
                'method' => ['GET', 'HEAD'],
                'params' => [],
                'throws' => [],
                'uri' => 'api',
                'name' => null,
            ],
            [
                'action' => 'Stubs\Pomek\Path2API\Controller@contact',
                'description' => join("\n", [
                    'Sending email message to given email address.',
                    '@see: mail() function',
                ]),
                'method' => ['POST'],
                'params' => [
                    '$email' => ['string']
                ],
                'throws' => ['\InvalidArgumentException'],
                'uri' => 'api/contact/{email}',
                'name' => null,
            ],
            [
                'action' => 'Closure',
                'description' => null,
                'method' => ['GET', 'POST', 'HEAD'],
                'params' => [],
                'throws' => [],
                'uri' => 'api/test/{id}',
                'name' => null,
            ],
        ]);
    }

    function getMatchers() {
        return [
            'returnDocs' => function (array $values, array $expect) {
                $numbers = count($values);

                if (count($expect) !== $numbers) {
                    return false;
                }

                for ($i = 0; $i < $numbers; ++$i) {
                    if ($values[$i] != $expect[$i]) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }

}
