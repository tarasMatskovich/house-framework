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
use houseframework\app\config\ConfigInterface;
use houseframework\app\eventlistener\EventListenerInterface;
use houseframework\app\request\builder\RequestBuilderInterface;
use houseframework\app\request\pipeline\builder\PipelineBuilderInterface;
use houseframework\app\response\WampResponse;
use houseframework\app\router\RouterInterface;
use React\EventLoop\Factory;
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
     * @var ConfigInterface
     */
    private $config;

    /**
     * WampApplication constructor.
     * @param ContainerInterface $container
     * @param RouterInterface $router
     * @param RequestBuilderInterface $requestBuilder
     * @param PipelineBuilderInterface $pipelineBuilder
     * @param ConfigInterface $config
     * @throws \Exception
     */
    public function __construct(
        ContainerInterface $container,
        RouterInterface $router,
        RequestBuilderInterface $requestBuilder,
        PipelineBuilderInterface $pipelineBuilder,
        ConfigInterface $config
    )
    {
        $this->container = $container;
        $this->router = $router;
        $this->requestBuilder = $requestBuilder;
        $this->pipelineBuilder = $pipelineBuilder;
        $this->config = $config;
        $this->beforeRun();
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function beforeRun()
    {
        $this->actions = $this->router->getRoutes();
        $realm = $this->config->get("transport:wamp:realm");
        $url = $this->config->get("transport:wamp:url");
        $this->container->set('application.eventLoop', Factory::create());
        $this->session = new Client($realm, $this->container->get('application.eventLoop'));
        $this->session->addTransportProvider(new PawlTransportProvider($url));
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
                    try {
                        $request = $this->requestBuilder->build();
                        $attributesFromArguments = $arguments[0] ?? null;
                        $attributes = [];
                        if ($attributesFromArguments) {
                            if (is_string($attributesFromArguments)) {
                                $attributes = json_decode($attributesFromArguments, null);
                            }
                            if (is_array($attributesFromArguments)) {
                                $attributes = (array)$attributesFromArguments;
                            }
                        }
                        $request = $this->requestBuilder->attachAttributesToRequest($request, $attributes);
                        $pipeline = $this->pipelineBuilder->build($actionRoute);
                        $responseData = $action($pipeline->process($request));
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
                try {
                    $request = $this->requestBuilder->build();
                    $attributesFromArguments = $arguments[0] ?? null;
                    $attributes = [];
                    if ($attributesFromArguments) {
                        if (is_string($attributesFromArguments)) {
                            $attributes = json_decode($attributesFromArguments, null);
                        }
                        if (is_array($attributesFromArguments)) {
                            $attributes = (array)$attributesFromArguments;
                        }
                    }
                    $request = $this->requestBuilder->attachAttributesToRequest($request, $attributes);
                    $responseData = $listener($request);
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
            });
        }
    }

}
