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
 * Class Pipeline
 * @package houseframework\app\request\pipeline
 */
class Pipeline implements PipelineInterface
{

    /**
     * @var ActionInterface[]
     */
    private $pipes = [];

    /**
     * @param ActionInterface $payload
     * @return static
     */
    public function pipe(ActionInterface $payload)
    {
        $this->pushPipe($payload);
        $pipeline = clone $this;
        return $pipeline;
    }

    /**
     * @param ActionInterface $payload
     */
    private function pushPipe(ActionInterface $payload)
    {
        $this->pipes[] = $payload;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function process(ServerRequestInterface $request)
    {
        $result = null;
        if (empty($this->pipes)) {
            return $request;
        }
        foreach ($this->pipes as $pipe) {
            if (null === $result) {
                $result = $pipe($request);
            } else {
                $result = $pipe($result);
            }
        }
        return $result;
    }

}
