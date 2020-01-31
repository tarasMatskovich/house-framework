<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 13:56
 */

namespace houseframework\app;


use GuzzleHttp\Psr7\Response;
use housedi\ContainerInterface;
use houseframework\app\request\builder\RequestBuilderInterface;
use houseframework\app\response\ResponseHandler;
use houseframework\app\router\RouterInterface;

/**
 * Class HttpApplication
 * @package houseframework\app
 */
class HttpApplication implements ApplicationInterface
{

    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @var RouterInterface $router
     */
    private $router;

    /**
     * @var RequestBuilderInterface
     */
    private $requestBuilder;

    /**
     * HttpApplication constructor.
     * @param ContainerInterface $container
     * @param RouterInterface $router
     * @param RequestBuilderInterface $requestBuilder
     */
    public function __construct(
        ContainerInterface $container,
        RouterInterface $router,
        RequestBuilderInterface $requestBuilder
    )
    {
        $this->container = $container;
        $this->router = $router;
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * @return void
     */
    public function run()
    {
        try {
            $request = $this->requestBuilder->build();
            $actionParams = $this->router->run($request);
            $action = $this->container->get($actionParams['action']);
            $request = $this->requestBuilder->attachAttributesToRequest(
                $request,
                $actionParams['queryParams'] ?? []
            );
            $result = $action($request);
            $response = new Response(
                200,
                [
                    'Content-Type' => 'application/json'
                ],
                \json_encode([
                    'status' => 'success',
                    'data' => $result
                ])
            );
            ResponseHandler::respond($response);
        } catch (\Exception $e) {
            $response = new Response(
                $this->getResponseCode($e),
                [
                    'Content-Type' => 'application/json'
                ],
                \json_encode([
                    'status' => 'error',
                    'data' => $e->getMessage()
                ])
            );
            ResponseHandler::respond($response);
        }
    }

    private function getResponseCode(\Exception $e)
    {
        $code = $e->getCode();
        if ($code < 100 || $code >= 599) {
            return 500;
        }
        return $code;
    }

}
