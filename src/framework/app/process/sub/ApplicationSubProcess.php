<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 17.02.2020
 * Time: 12:14
 */

namespace houseframework\app\process\sub;


use houseframework\app\process\app\SubProcessApplication;
use houseframework\app\process\app\SubProcessApplicationInterface;
use houseframework\app\process\payload\PayloadKeysEnum;
use React\EventLoop\LoopInterface;
use function React\Promise\resolve;
use WyriHaximus\React\ChildProcess\Messenger\ChildInterface;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;
use WyriHaximus\React\ChildProcess\Messenger\Messenger;

/**
 * Class ApplicationSubProcess
 * @package houseframework\app\process\sub
 */
class ApplicationSubProcess implements ChildInterface
{

    const CONFIG = 'config';

    const RPC_RUN = 'run';

    const RPC_BUILD = 'build';

    /**
     * @var SubProcessApplicationInterface
     */
    private $app;

    /**
     * ApplicationSubProcess constructor.
     * @param Messenger $messenger
     * @param LoopInterface $loop
     */
    public function __construct(Messenger $messenger, LoopInterface $loop)
    {
        $messenger->registerRpc(static::RPC_BUILD, function (Payload $payload) {
            $this->app = $this->build($payload);
        });
        $messenger->registerRpc(static::RPC_RUN, function (Payload $payload) {
            return resolve($this->app->run($payload));
        });
    }

    /**
     * @param Payload $payload
     * @return SubProcessApplicationInterface
     */
    private function build(Payload $payload)
    {
        $container = $payload[PayloadKeysEnum::CONTAINER_DEF];
        $requestBuilder = $payload[PayloadKeysEnum::REQUEST_BUILDER_DEF];
        $pipelineBuilder = $payload[PayloadKeysEnum::PIPELINE_BUILDER_DEF];
        return new SubProcessApplication(
            $container,
            $requestBuilder,
            $pipelineBuilder
        );
    }

    /**
     * @param Messenger $messenger
     * @param LoopInterface $loop
     */
    public static function create(Messenger $messenger, LoopInterface $loop)
    {
        new static($messenger, $loop);
    }
}
