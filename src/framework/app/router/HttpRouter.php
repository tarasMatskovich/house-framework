<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 13:43
 */

namespace houseframework\app\router;


use houseframework\app\router\enum\HttpMethodsEnum;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class HttpRouter
 * @package houseframework\app\router
 */
class HttpRouter implements RouterInterface
{

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var array $httpRoutes
     */
    private $httpRoutes;

    /**
     * @var array $actionParams
     */
    private $actionParams;

    /**
     * HttpRouter constructor.
     * @param RouterInterface $router
     * @param array $httpRoutes
     */
    public function __construct(
        RouterInterface $router,
        array $httpRoutes
    )
    {
        $this->router = $router;
        foreach ($httpRoutes as $route => $params) {
            $this->add($route, $params);
        }
    }

    /**
     * @param $route
     * @param $params
     */
    private function add($route, $params)
    {
        $route = '#^'.$route.'$#';
        $this->httpRoutes[$route] = $params;
    }

    /**
     * @param array $matches
     * @return array
     */
    private function filterMatches(array $matches)
    {
        foreach ($matches as $key => $match) {
            if (empty($match))
                unset($matches[$key]);
        }
        return $matches;
    }

    /**
     * @param string $route
     * @return array|mixed
     */
    private function makeQueryParams(string $route)
    {
        preg_match_all("/\{\w+\}/", $route, $queryParams);
        $queryParams = $this->filterMatches($queryParams);
        $queryParams = reset($queryParams);
        return $queryParams;
    }

    /**
     * @param array $queryParams
     * @param string $route
     * @param string $url
     */
    private function attachQueryParamsToActionParams(array $queryParams, string $route, string $url)
    {
        $replacement = "";
        for ($i = 1; $i <= count($queryParams); $i++) {
            $replacement .= "$" . $i . ";";
        }
        $replacement = substr($replacement, 0, -1);
        $result = preg_replace($route, $replacement, $url);
        $result = explode(";", $result);
        foreach ($result as $key => $value) {
            unset($result[$key]);
            $newKey = $queryParams[$key];
            $newKey = substr($newKey, 0, -1);
            $newKey = substr($newKey, 1);
            $result[$newKey] = $value;
        }
        $this->actionParams['queryParams'] = $result;
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    private function match(ServerRequestInterface $request)
    {
        $url = trim($request->getRequestTarget(), '/');
        foreach ($this->httpRoutes as $route => $actionParams) {
            $queryParams = $this->makeQueryParams($route);
            if ($queryParams) {
                foreach ($queryParams as $param) {
                    $route = str_replace($param, "(\w+)", $route);
                }
            }
            if (preg_match($route, $url)) {
                $this->actionParams = $actionParams;
                if ($queryParams) {
                    $this->attachQueryParamsToActionParams($queryParams, $route, $url);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $method
     * @param ServerRequestInterface $request
     * @throws MethodNotAllowedException
     */
    private function validateMethod(string $method, ServerRequestInterface $request)
    {
        if (!in_array($method, HttpMethodsEnum::AVAILABLE_METHODS)) {
            throw new MethodNotAllowedException("Method {$request->getMethod()} is not available for this action", 403);
        }
        if ($method !== $request->getMethod()) {
            throw new MethodNotAllowedException("Method {$request->getMethod()} is not available for this action", 403);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return array
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     */
    public function run(ServerRequestInterface $request)
    {
        $params = [];
        if ($this->match($request)) {
            foreach ($this->actionParams as $method => $action) {
                $this->validateMethod($method, $request);
                $routerParams = $this->router->run($request->withAttribute('action', $action));
                $params['action'] = $routerParams['action'];
                break;
            }
            if (isset($this->actionParams['queryParams']))
                $params['queryParams'] = $this->actionParams['queryParams'];
            return $params;
        }
        throw new NotFoundException("Requested resource was not found", 404);
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->router->getRoutes();
    }

}
