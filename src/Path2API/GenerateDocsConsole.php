<?php

namespace Pomek\Path2API;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Console\Command;

class GenerateDocsConsole extends Command
{

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

        $this->getRoutes();
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
        $results = array();

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
