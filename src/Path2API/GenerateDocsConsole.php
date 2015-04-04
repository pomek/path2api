<?php

namespace Pomek\Path2API;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Filesystem\Filesystem;
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
    protected $description = 'Generate file with description of every route which URL\'s begin api/*. It based on PHPDoc of callback methods.';

    /**
     * An array of all the registered routes.
     *
     * @var \Illuminate\Routing\RouteCollection
     */
    protected $routes;

    /**
     * An array of package configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Create a new route command instance.
     *
     * @param \Illuminate\Routing\Router $router
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Router $router, ConfigRepository $config, Filesystem $filesystem)
    {
        parent::__construct();

        $this->routes = $router->getRoutes();
        $this->config = $config->get('path2api');
        $this->filesystem = $filesystem;
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

        $file_contents = [
            $this->config['before'],
        ];

        foreach ($this->getRoutes() as $route) {
            $route = $this->parseRoute($route);
            $template = $this->config['template'];

            $file_contents[] = $template($route['uri'], $route['description'], $route['params'], $route['throws']);
        }

        $file_contents[] = $this->config['after'];

        $this->filesystem->put($this->config['file'], join("\n", $file_contents));
    }

    /**
     * Returns an array with attached documentation for every route.
     *
     * @return array
     */
    public function getRoutesWithDocs()
    {
        $results = [];

        foreach ($this->getRoutes() as $route) {
            $results[] = $this->parseRoute($route);
        }

        return $results;
    }

    /**
     * Returns parsed route - attach documentation to route.
     * It doesn't work with inline Closures.
     *
     * @param array $route
     * @return array
     */
    protected function parseRoute(array $route)
    {
        $build_array = function ($descprition, array $throws, array $params) use (&$route) {
            return array_merge($route, [
                'description' => $descprition,
                'throws' => $throws,
                'params' => $params,
            ]);
        };

        $action = $route['action'];

        if ('Closure' === $action || false === strpos($action, '@')) {
            return $build_array(null, [], []);
        }

        list($class, $method) = explode('@', $action);

        $ref_class = new \ReflectionClass($class);
        $parser = new PhpDocParser($ref_class->getMethod($method));

        return $build_array($parser->getDescription(), $parser->getThrows(), $parser->getParams());
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
        if (!str_contains($route['uri'], $this->config['prefix'])) {
            return;
        }

        return $route;
    }
}
