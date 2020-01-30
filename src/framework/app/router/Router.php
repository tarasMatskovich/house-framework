<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 11:44
 */

namespace houseframework\app\router;


use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Router
 * @package houseframework\app\router
 */
class Router implements RouterInterface
{

    /**
     * @var array
     */
    private $routes;

    /**
     * Router constructor.
     * @param array $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    private function match(ServerRequestInterface $request)
    {
        $action = $request->getAttribute('action');
        if (!$action)
            return false;
        return isset($this->routes[$action]);
    }

    /**
     * @param ServerRequestInterface $request
     * @return array
     * @throws NotFoundException
     */
    public function run(ServerRequestInterface $request)
    {
        $params = [];
        if ($this->match($request)) {
            $action = $request->getAttribute('action');
            $params['action'] = $this->routes[$action];
            return $params;
        }
        throw new NotFoundException("Requested action was not found");
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

}
