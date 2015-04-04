<?php

namespace Pomek\Path2API;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Console\Command;

class GenerateDocsConsole extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'path2api:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate file with description of every route which URL\'s begin api/*. It based on PHPDoc of callback method.';

    /**
     * An array of all the registered routes.
     *
     * @var \Illuminate\Routing\RouteCollection
     */
    protected $routes;

    /**
     * Create a new route command instance.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function __construct(Router $router)
    {
        parent::__construct();

        $this->routes = $router->getRoutes();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (0 === count($this->routes)) {
            $this->error("Your application doesn't have any routes.");
            return;
        }


    }

    public function getRoutesWithDocs()
    {
        $results = [];

        foreach ($this->getRoutes() as $route) {
            $results[] = $this->parseRoute($route);
        }

        return $results;
    }

    protected function parseRoute(array $route)
    {
        $action = $route['action'];

        if ('Closure' === $action) {
            return $route;
        }

        if (false === strpos($action, '@')) {
            return $route;
        }

        list($class, $method) = explode('@', $action);

        $ref_class = new \ReflectionClass($class);
        $parser = new PhpDocParser($ref_class->getMethod($method));

        return array_merge($route, [
            'description' => $parser->getDescription(),
            'throws' => $parser->getThrows(),
            'params' => $parser->getParams(),
        ]);
    }

    /**
     * Get the route information for a given route.
     *
     * @param  \Illuminate\Routing\Route $route
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        return $this->filterRoute([
            'uri' => $route->uri(),
            'method' => $route->methods(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ]);
    }

    /**
     * Compile the routes into a displayable format.
     *
     * @return array
     */
    protected function getRoutes()
    {
        $results = [];

        foreach ($this->routes as $route) {
            $results[] = $this->getRouteInformation($route);
        }

        return array_filter($results);
    }

    /**
     * Filter the route by URI.
     *
     * @param array $route
     * @return array|null
     */
    protected function filterRoute(array $route)
    {
        if (!str_contains($route['uri'], 'api')) {
            return;
        }

        return $route;
    }
}
