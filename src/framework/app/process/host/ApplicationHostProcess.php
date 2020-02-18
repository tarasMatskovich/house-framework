<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 17.02.2020
 * Time: 12:14
 */

namespace houseframework\app\process\host;


use housedi\ContainerInterface;
use houseframework\app\config\ConfigInterface;
use houseframework\app\eventlistener\EventListenerInterface;
use houseframework\app\process\payload\PayloadKeysEnum;
use houseframework\app\process\sub\ApplicationSubProcess;
use houseframework\app\request\builder\RequestBuilderInterface;
use houseframework\app\request\pipeline\builder\PipelineBuilderInterface;
use houseframework\app\router\RouterInterface;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use Thruway\ClientSession;
use Thruway\Peer\Client;
use Thruway\Transport\PawlTransportProvider;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Factory;
use WyriHaximus\React\ChildProcess\Messenger\Messages\Payload;
use WyriHaximus\React\ChildProcess\Pool\PoolInterface;
use WyriHaximus\React\ChildProcess\Pool\WorkerInterface;

/**
 * Class ApplicationHostProcess
 * @package houseframework\app\process\host
 */
class ApplicationHostProcess implements ApplicationHostProcessInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var array
     */
    private $actions;

    /**
     * ApplicationHostProcess constructor.
     * @param ContainerInterface $container
     * @param RouterInterface $router
     * @param RequestBuilderInterface $requestBuilder
     * @param PipelineBuilderInterface $pipelineBuilder
     * @param ConfigInterface $config
     * @param PoolInterface $pool
     * @param array $parameters
     */
    public function __construct(
        ContainerInterface $container,
        RouterInterface $router,
        RequestBuilderInterface $requestBuilder,
        PipelineBuilderInterface $pipelineBuilder,
        ConfigInterface $config,
        PoolInterface $pool,
        array $parameters
    )
    {
        $this->container = $container;
        $this->router = $router;
        $this->config = $config;
        $this->pool = $pool;
        $this->loop = $this->container->get('application.eventLoop');
        $this->pool->on('worker', function (WorkerInterface $worker) use ($parameters, $requestBuilder, $pipelineBuilder) {
            $worker->rpc(Factory::rpc(
                ApplicationSubProcess::RPC_BUILD,
                [
                    ApplicationSubProcess::CONFIG => $parameters,
                    PayloadKeysEnum::CONTAINER_DEF => \Opis\Closure\serialize($this->container),
                    PayloadKeysEnum::REQUEST_BUILDER_DEF => \Opis\Closure\serialize($requestBuilder),
                    PayloadKeysEnum::PIPELINE_BUILDER_DEF => \Opis\Closure\serialize($pipelineBuilder)
                ]
            ));
        });
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function start()
    {
        $this->actions = $this->router->getRoutes();
        $realm = $this->config->get("transport:wamp:realm");
        $url = $this->config->get("transport:wamp:url");
        $client = new Client($realm, $this->loop);
        $client->addTransportProvider(new PawlTransportProvider($url));
        $client->on('open', function (ClientSession $session) {
            $this->container->set('application.clientSession', $session);
            $this->registerActions($session);
            $this->registerListeners($session);
        });
        $client->start();
    }

    /**
     * @param string $action
     * @param $attributes
     * @param ClientSession $clientSession
     * @return PromiseInterface
     */
    public function run(string $action, $attributes, ClientSession $clientSession)
    {
        return $this->pool->rpc(Factory::rpc(
            ApplicationSubProcess::RPC_RUN,
            [
                PayloadKeysEnum::ACTION => $action,
                PayloadKeysEnum::ATTRIBUTES => $attributes,
                PayloadKeysEnum::CLIENT_SESSION => \Opis\Closure\serialize($clientSession)
            ])
        )->then(function (Payload $payload) {
            return $payload->getPayload();
        });
    }

    /**
     * @param ClientSession $session
     */
    private function registerActions(ClientSession $session)
    {
        foreach ($this->actions as $key => $action) {
            $session->register($key, function ($arguments) use ($action, $session) {
                return $this->run($action, $arguments, $session);
            });
        }
    }

    /**
     * @param ClientSession $session
     */
    private function registerListeners(ClientSession $session)
    {
        /**
         * @var EventListenerInterface $eventListener
         */
        $eventListener = $this->container->get(EventListenerInterface::class);
        $channels = $eventListener->getChannels();
        foreach ($channels as $channelKey => $channelValue) {
            $listener = $channelValue;
            $session->subscribe($channelKey, function ($arguments) use ($listener, $session) {
                return $this->run($listener, $arguments, $session);
            });
        }
    }
}
