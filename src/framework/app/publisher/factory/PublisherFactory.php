<?php
/**
 * Created by PhpStorm.
 * User: t.matskovich
 * Date: 07.02.2020
 * Time: 17:27
 */

namespace houseframework\app\publisher\factory;


use houseframework\app\factory\enum\ApplicationTypesEnum;
use houseframework\app\publisher\PublisherInterface;

/**
 * Class PublisherFactory
 * @package houseframework\app\publisher\factory
 */
class PublisherFactory implements PublisherFactoryInterface
{

    /**
     * @var PublisherInterface
     */
    private $wampPublisher;

    /**
     * PublisherFactory constructor.
     * @param PublisherInterface $wampPublisher
     */
    public function __construct(
        PublisherInterface $wampPublisher
    )
    {
        $this->wampPublisher = $wampPublisher;
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
                return $this->wampPublisher;
                break;
            default:
                throw new \Exception('Unknown application type', 500);
        }
    }
}
