<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 30.01.2020
 * Time: 13:48
 */

namespace houseframework\app\factory;


use housedi\ContainerInterface;
use houseframework\app\ApplicationInterface;
use houseframework\app\factory\enum\ApplicationTypesEnum;
use houseframework\app\HttpApplication;
use houseframework\app\request\builder\RequestBuilderInterface;
use houseframework\app\request\pipeline\builder\PipelineBuilderInterface;
use houseframework\app\router\RouterInterface;
use houseframework\app\WampApplication;

/**
 * Class ApplicationFactory
 * @package houseframework\app\factory
 */
class ApplicationFactory implements ApplicationFactoryInterface
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
     * @var PipelineBuilderInterface
     */
    private $pipelineBuilder;

    /**
     * @var RequestBuilderInterface
     */
    private $requestBuilder;

    /**
     * ApplicationFactory constructor.
     * @param ContainerInterface $container
     * @param RouterInterface $router
     * @param PipelineBuilderInterface $pipelineBuilder
     * @param RequestBuilderInterface $requestBuilder
     */
    public function __construct(
        ContainerInterface $container,
        RouterInterface $router,
        PipelineBuilderInterface $pipelineBuilder,
        RequestBuilderInterface $requestBuilder
    )
    {
        $this->container = $container;
        $this->router = $router;
        $this->pipelineBuilder = $pipelineBuilder;
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * @param string $applicationType
     * @return ApplicationInterface
     * @throws \Exception
     */
    public function make(string $applicationType)
    {
        switch ($applicationType) {
            case ApplicationTypesEnum::APP_WAMP:
                return new WampApplication(
                    $this->container,
                    $this->router,
                    $this->requestBuilder,
                    $this->pipelineBuilder
                );
                break;
            case ApplicationTypesEnum::APP_HTTP:
                return new HttpApplication(
                    $this->container,
                    $this->router,
                    $this->requestBuilder
                );
            default:
                throw new ApplicationFactoryException("Undefined application type!");
                break;
        }
    }
}
