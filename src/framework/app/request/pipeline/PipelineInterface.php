<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 15:33
 */

namespace houseframework\app\request\pipeline;


use houseframework\action\ActionInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface PipelineInterface
 * @package houseframework\app\request\pipeline
 */
interface PipelineInterface
{

    /**
     * @param ActionInterface $payload
     * @return static
     */
    public function pipe(ActionInterface $payload);

    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function process(ServerRequestInterface $request);

}
