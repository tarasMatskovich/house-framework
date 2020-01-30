<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 13:55
 */

namespace houseframework\app;


use Evenement\EventEmitterInterface;
use housedi\ContainerInterface;
use houseframework\action\ActionInterface;
use houseframework\app\eventlistener\EventListenerInterface;
use houseframework\app\request\builder\RequestBuilderInterface;
use houseframework\app\request\pipeline\builder\PipelineBuilderInterface;
use houseframework\app\response\Response;
use houseframework\app\router\RouterInterface;
use Thruway\ClientSession;
use Thruway\Peer\Client;
use Thruway\Peer\ClientInterface;
use Thruway\Transport\PawlTransportProvider;

/**
 * Class WampApplication
 * @package houseframework\app
 */
class WampApplication implements ApplicationInterface
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
     * @var ActionInterface[]
     */
    private $actions;

    /**
     * @var RequestBuilderInterface
     */
    private $requestBuilder;

    /**
     * @var ClientInterface|EventEmitterInterface
     */
    private $session;

    /**
     * @var PipelineBuilderInterface
     */
    private $pipelineBuilder;

    /**
     * WampApplication constructor.
     * @param ContainerInterface $container
     * @param RouterInterface $router
     * @param RequestBuilderInterface $requestBuilder
     * @param PipelineBuilderInterface $pipelineBuilder
     * @throws \Exception
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
        $this->beforeRun();
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function beforeRun()
    {
        $this->actions = $this->router->getRoutes();
        $this->session = new Client("realm1", $this->container->get('eventLoop'));
        $this->session->addTransportProvider(new PawlTransportProvider("ws://127.0.0.1:8080/ws"));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $this->session->on('open', function (ClientSession $session) {
            $this->container->set('application.clientSession', $session);
            foreach ($this->actions as $key => $action) {
                $actionRoute = $action;
                $action = $this->container->get($action);
                $session->register($key, function ($arguments) use ($action, $actionRoute) {
                    $request = $this->requestBuilder->build();
                    $attributesFromArguments = $arguments[0] ?? null;
                    $attributes = $attributesFromArguments ? json_decode($attributesFromArguments, true) : [];
                    $request = $this->requestBuilder->attachAttributesToRequest($request, $attributes);
                    $pipeline = $this->pipelineBuilder->build($actionRoute);
                    $responseData = $action($pipeline->process($request));
                    return new Response($responseData);
                });
            }
            $this->registerListeners($session);
        });
        $this->session->start();
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
            $listener = $this->container->get($channelValue);
            $session->subscribe($channelKey, function ($arguments) use ($listener) {
                $request = $this->requestBuilder->build();
                $attributesFromArguments = $arguments[0] ?? null;
                $attributes = $attributesFromArguments ? json_decode($attributesFromArguments, true) : [];
                $request = $this->requestBuilder->attachAttributesToRequest($request, $attributes);
                $responseData = $listener($request);
                return new Response($responseData);
            });
        }
    }

}
