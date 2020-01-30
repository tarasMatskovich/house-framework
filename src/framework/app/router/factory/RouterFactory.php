<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 13:42
 */

namespace houseframework\app\router\factory;


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
     * @param string $buildKey
     * @param array $routes
     * @param array $httpRoutes
     * @return RouterInterface
     * @throws RouterFactoryException
     */
    public function make(string $buildKey, array $routes, array $httpRoutes = [])
    {
        switch ($buildKey) {
            case ApplicationTypesEnum::APP_HTTP:
                return new HttpRouter(new Router($routes), $httpRoutes);
            case ApplicationTypesEnum::APP_WAMP:
                return new Router($routes);
        }
        throw new RouterFactoryException("Undefined application build key");
    }

}
