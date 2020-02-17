<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 17.02.2020
 * Time: 12:12
 */

namespace houseframework\app\process\app;


use housedi\ContainerInterface;
use houseframework\app\process\payload\PayloadKeysEnum;
use houseframework\app\request\builder\RequestBuilderInterface;
use houseframework\app\request\pipeline\builder\PipelineBuilderInterface;
use houseframework\app\request\ValidatedRequestMessage;
use houseframework\app\request\validator\Validator;
use houseframework\app\response\WampResponse;
use Psr\Http\Message\ServerRequestInterface;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;

/**
 * Class SubProcessApplication
 * @package houseframework\app\process\app
 */
class SubProcessApplication implements SubProcessApplicationInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestBuilderInterface
     */
    private $requestBuilder;

    /**
     * @var PipelineBuilderInterface
     */
    private $pipelineBuilder;

    /**
     * SubProcessApplication constructor.
     * @param ContainerInterface $container
     * @param RequestBuilderInterface $requestBuilder
     * @param PipelineBuilderInterface $pipelineBuilder
     */
    public function __construct(
        ContainerInterface $container,
        RequestBuilderInterface $requestBuilder,
        PipelineBuilderInterface $pipelineBuilder
    )
    {
        $this->container = $container;
        $this->requestBuilder = $requestBuilder;
        $this->pipelineBuilder = $pipelineBuilder;
    }

    /**
     * @param Payload $payload
     * @return WampResponse
     */
    public function run(Payload $payload)
    {
        try {
            $clientSession = $payload[PayloadKeysEnum::CLIENT_SESSION];
            $this->container->set('application.clientSession', $clientSession);
            $actionRoute = $payload[PayloadKeysEnum::ACTION];
            $action = $this->container->get($actionRoute);
            $arguments = $payload[PayloadKeysEnum::ATTRIBUTES];
            $request = $this->requestBuilder->build();
            $attributesFromArguments = $arguments[0] ?? null;
            $attributes = [];
            if ($attributesFromArguments) {
                if (is_string($attributesFromArguments)) {
                    $attributes = json_decode($attributesFromArguments, null);
                } else {
                    $attributes = (array)$attributesFromArguments;
                }
            }
            $request = $this->requestBuilder->attachAttributesToRequest($request, $attributes);
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
                        $specialRequest = $this->requestBuilder->attachAttributesToRequest($specialRequest, $attributes);
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
            $responseData = $action($pipelineResult);
            return new WampResponse([
                'status' => 'success',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            return new WampResponse([
                'status' => 'error',
                'data' => $e->getMessage()
            ]);
        }
    }
}
