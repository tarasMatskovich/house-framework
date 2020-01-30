<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 15:39
 */

namespace houseframework\app\request\pipeline\builder;


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
     * @var MiddlewareInterface[]
     */
    private $middlewares;

    /**
     * @var array
     */
    private $skippedActions = [];

    /**
     * PipelineBuilder constructor.
     * @param MiddlewareInterface[] $middlewares
     * @param array $skippedActions
     */
    public function __construct(array $middlewares, array $skippedActions)
    {
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
        foreach ($this->middlewares as $middleware) {
            $skippedMiddlewaresForAction = $this->skippedActions[$action] ?? [];
            if (!\in_array(get_class($middleware), $skippedMiddlewaresForAction)) {
                $pipeline = $pipeline->pipe($middleware);
            }
        }
        return $pipeline;
    }

}
