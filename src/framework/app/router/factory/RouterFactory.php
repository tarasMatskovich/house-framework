<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 13:42
 */

namespace houseframework\app\router\factory;


use houseframework\app\factory\enum\ApplicationTypesEnum;
use houseframework\app\router\HttpRouter;
use houseframework\app\router\Router;
use houseframework\app\router\RouterInterface;

/**
 * Class RouterFactory
 * @package houseframework\app\router\factory
 */
class RouterFactory implements RouterFactoryInterface
{

    /**
     * @var array
     */
    private $routes;

    /**
     * @var array
     */
    private $httpRoutes;

    /**
     * RouterFactory constructor.
     * @param array $routes
     * @param array $httpRoutes
     */
    public function __construct(array $routes = [], $httpRoutes = [])
    {
        $this->routes = $routes;
        $this->httpRoutes = $httpRoutes;
    }

    /**
     * @param string $buildKey
     * @return RouterInterface
     * @throws RouterFactoryException
     */
    public function make(string $buildKey)
    {
        switch ($buildKey) {
            case ApplicationTypesEnum::APP_HTTP:
                return new HttpRouter(new Router($this->routes), $this->httpRoutes);
            case ApplicationTypesEnum::APP_WAMP:
                return new Router($this->routes);
        }
        throw new RouterFactoryException("Undefined application build key");
    }

}
