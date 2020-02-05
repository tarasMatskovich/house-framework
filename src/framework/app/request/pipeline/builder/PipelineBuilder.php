<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 15:39
 */

namespace houseframework\app\request\pipeline\builder;


use housedi\ContainerInterface;
use houseframework\app\request\middleware\MiddlewareInterface;
use houseframework\app\request\pipeline\Pipeline;
use houseframework\app\request\pipeline\PipelineInterface;

/**
 * Class PipelineBuilder
 * @package houseframework\app\request\pipeline\builder
 */
class PipelineBuilder implements PipelineBuilderInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $globalMiddlewares;

    /**
     * @var array
     */
    private $middlewares;

    /**
     * @var array
     */
    private $skippedActions;

    /**
     * PipelineBuilder constructor.
     * @param ContainerInterface $container
     * @param array $globalMiddlewares
     * @param array $middlewares
     * @param array $skippedActions
     */
    public function __construct(ContainerInterface $container, array $globalMiddlewares, array $middlewares, array $skippedActions = [])
    {
        $this->container = $container;
        $this->globalMiddlewares = $globalMiddlewares;
        $this->middlewares = $middlewares;
        $this->skippedActions = $skippedActions;
    }


    /**
     * @param string $action
     * @return PipelineInterface
     */
    public function build(string $action)
    {
        $pipeline = new Pipeline();
        foreach ($this->globalMiddlewares as $middleware) {
            $skippedMiddlewaresForAction = $this->skippedActions[$action] ?? [];
            if (!\in_array($middleware, $skippedMiddlewaresForAction)) {
                try {
                    $middleware = $this->container->get($middleware);
                    if ($this->isMiddlewareValid($middleware)) {
                        $pipeline = $pipeline->pipe($middleware);
                    }
                } catch (\Exception $e) {continue;}
            }
        }
        $middlewaresForAction = $this->middlewares[$action] ?? [];
        if (\is_array($middlewaresForAction) && !empty($middlewaresForAction)) {
            foreach ($middlewaresForAction as $middleware) {
                try {
                    $middleware = $this->container->get($middleware);
                    if ($this->isMiddlewareValid($middleware)) {
                        $pipeline = $pipeline->pipe($middleware);
                    }
                } catch (\Exception $e) {continue;}
            }
        }
        return $pipeline;
    }

    /**
     * @param $middleware
     * @return bool
     */
    private function isMiddlewareValid($middleware)
    {
        return $middleware instanceof MiddlewareInterface;
    }

}
