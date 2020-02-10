<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 07.02.2020
 * Time: 17:27
 */

namespace houseframework\app\publisher\factory;


use houseframework\app\config\ConfigInterface;
use houseframework\app\factory\enum\ApplicationTypesEnum;
use houseframework\app\publisher\PublisherInterface;
use houseframework\app\publisher\WampPublisher;

/**
 * Class PublisherFactory
 * @package houseframework\app\publisher\factory
 */
class PublisherFactory implements PublisherFactoryInterface
{

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * PublisherFactory constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $applicationKey
     * @return PublisherInterface
     * @throws \Exception
     */
    public function makePublisher(string $applicationKey)
    {
        switch ($applicationKey) {
            case ApplicationTypesEnum::APP_WAMP:
                return new WampPublisher(
                    $this->config->get('application.clientSession')
                );
                break;
            default:
                throw new \Exception('This application type does not support publishing', 500);
        }
    }
}
