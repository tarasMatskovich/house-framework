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
use houseframework\app\request\pipeline\builder\PipelineBuilderInterface;
use houseframework\app\request\ValidatedRequestMessage;
use houseframework\app\request\validator\Validator;
use houseframework\app\response\ResponseHandler;
use houseframework\app\router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * @var PipelineBuilderInterface
     */
    private $pipelineBuilder;

    /**
     * HttpApplication constructor.
     * @param ContainerInterface $container
     * @param RouterInterface $router
     * @param RequestBuilderInterface $requestBuilder
     * @param PipelineBuilderInterface $pipelineBuilder
     */
    public function __construct(
        ContainerInterface $container,
        RouterInterface $router,
        RequestBuilderInterface $requestBuilder,
        PipelineBuilderInterface $pipelineBuilder
    )
    {
        $this->container = $container;
        $this->router = $router;
        $this->requestBuilder = $requestBuilder;
        $this->pipelineBuilder = $pipelineBuilder;
    }

    /**
     * @return void
     */
    public function run()
    {
        try {
            $request = $this->requestBuilder->build();
            $actionParams = $this->router->run($request);
            $actionRoute = $actionParams['action'];
            $action = $this->container->get($actionRoute);
            $request = $this->requestBuilder->attachAttributesToRequest(
                $request,
                $actionParams['queryParams'] ?? []
            );
            $pipeline = $this->pipelineBuilder->build($actionRoute);
            $reflectionClass = new \ReflectionClass($action);
            $pipelineResult = null;
            $invokable = $reflectionClass->getMethod('__invoke');
            if ($invokable) {
                $invokableParameters = $invokable->getParameters();
                $invokableParameter = $invokableParameters[0] ?? null;
                if ($invokableParameter) {
                    $className = $invokableParameter->getClass()->getName();
                    if ($className !== ServerRequestInterface::class) {
                        if (!class_exists($className)) {
                            throw new \Exception('Class: ' . $className . ' does not exist in action', 500);
                        }
                        $specialRequest = $this->requestBuilder->buildSpecialRequest($className);
                        if (!$specialRequest instanceof ValidatedRequestMessage) {
                            throw new \Exception("Class: " . $className . ' must extends ' . ValidatedRequestMessage::class, 500);
                        }
                        $specialRequest = $this->requestBuilder->attachAttributesToRequest($specialRequest, $actionParams['queryParams'] ?? []);
                        $requestValidator = new Validator();
                        if (!$requestValidator->validate($specialRequest, $specialRequest->getRules())) {
                            throw new \Exception(json_encode($requestValidator->getErrors()), 500);
                        }
                        $pipelineResult = $pipeline->process($specialRequest);
                    } else {
                        $pipelineResult = $pipeline->process($request);
                    }
                }
            }
            if (!$pipelineResult) {
                throw new \Exception('Pipeline build was failed. Check your action: ' . $actionRoute, 500);
            }
            $result = $action($pipelineResult);
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

    /**
     * @param \Exception $e
     * @return int
     */
    private function getResponseCode(\Exception $e)
    {
        $code = $e->getCode();
        if ($code < 100 || $code >= 599) {
            return 500;
        }
        return $code;
    }

}
